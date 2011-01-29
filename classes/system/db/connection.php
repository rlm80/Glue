<?php

namespace Glue\System\DB;

use \PDO;

/**
 * Base database connection class.
 *
 * A connection object is a PDO instance connected to a specific database. Connections provide
 * a unified interface for database introspection and functions to generate RDBMS specific SQL
 * from data structures representing pieces of SQL queries.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class Connection extends PDO {
	/**
	 * @var array Table instances cache.
	 */
	protected $tables = array();

	/**
	 * @var array Table list cache.
	 */
	protected $table_list;

	/**
	 * @var string Connection id.
	 */
	protected $id;

	/**
	 * @var string Username used to connect to the database.
	 */
	protected $username;

	/**
	 * @var string Password used to connect to the database.
	 */
	protected $password;

	/**
	 * @var string Options array passed to PDO constructor.
	 */
	protected $options;

	/**
	 * @var string Connection charset.
	 */
	protected $charset;

	/**
	 * Constructor.
	 *
	 * @param $id Connection identifier.
	 */
	public function __construct($id) {
		// Set connection ID :
		$this->id = $id;

		// Initialize connection data :
		$this->init();

		// Build DSN :
		$dsn = $this->dsn();

		// Call parent constructor to establish connection :
		parent::__construct($dsn, $this->username, $this->password, $this->options);

		// Unset username and password to make sure they're not dumped accidentaly on display :
		unset($this->username);
		unset($this->password);

		// Set attributes :
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Glue\\DB\\Statement', array($this)));

		// Set charset :
		$this->set_charset();
	}

	/**
	 * Builds DSN once all properties have been set.
	 */
	abstract protected function dsn();

	/**
	 * Connection data initialization function, meant to be redefined.
	 */
	protected function init() {
		if ( ! isset($this->username))	$this->username	= $this->default_username();
		if ( ! isset($this->password))	$this->password	= $this->default_password();
		if ( ! isset($this->options))	$this->options	= $this->default_options();
		if ( ! isset($this->charset))	$this->charset	= $this->default_charset();
	}

	/**
	 * Default username.
	 */
	protected function default_username() {
		return 'root';
	}

	/**
	 * Default password.
	 */
	protected function default_password() {
		return '';
	}

	/**
	 * Default options.
	 */
	protected function default_options() {
		return null;
	}

	/**
	 * Default options.
	 */
	protected function default_charset() {
		return 'utf8';
	}

	/**
	 * Sets connection charset.
	 */
	protected function set_charset() {
		$this->exec('SET NAMES ' . $this->quote($this->charset));
	}

	/** 
	 * Overridden to accept fragments as well as SQL strings.
	 * 
	 * @see PDO::prepare()
	 */
	public function prepare() {
		$args = func_get_args();
		$args[0] = is_string($args[0]) ? $args[0] : $this->compile($args[0]);
		return call_user_func_array('parent::prepare', $args);
	}

	/** 
	 * Overridden to accept fragments as well as SQL strings.
	 * 
	 * @see PDO::query()
	 */	
	public function query() {
		$args = func_get_args();
		$args[0] = is_string($args[0]) ? $args[0] : $this->compile($args[0]);
		return call_user_func_array('parent::query', $args);
	}

	/** 
	 * Overridden to accept fragments as well as SQL strings.
	 * 
	 * @see PDO::exec()
	 */	
	public function exec() {
		$args = func_get_args();
		$args[0] = is_string($args[0]) ? $args[0] : $this->compile($args[0]);
		return call_user_func_array('parent::exec', $args);
	}

	/**
	 * Returns all tables defined on this connection as an array of table objects indexed by table name.
	 *
	 * @return array
	 */
	public function tables() {
		return array_map(array($this, 'table'), $this->table_list());
	}

	/**
	 * Whether or not the table exists on this connection.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function table_exists($name) {
		return array_key_exists($name, $this->table_list());
	}

	/**
	 * Returns an array with all available tables on this connection, as an array of table names indexed
	 * by name.
	 *
	 * @return array
	 */
	public function table_list() {
		if( ! isset($this->table_list))
			$this->table_list = $this->table_list_from_cache();
		return $this->table_list;
	}

	/**
	 * Loads the table list from the disk cache. If it isn't there already, creates
	 * a new cache entry for it.
	 *
	 * @return array
	 */
	protected function table_list_from_cache() {
		$path = 'db/tables/list/' . $this->id . '.tmp';
		if ( ! $list = \Glue\Core::get_cache_entry($path)) {
			$list = $this->table_list_from_db();
			\Glue\Core::create_cache_entry($path, $list);
		}
		return $list;
	}

	/**
	 * Retruns table list by database introspection as an array of table names indexed by table name.
	 *
	 * @return array
	 */
	abstract protected function table_list_from_db();

	/**
	 * Loads a table object, stores it in cache, and returns it.
	 *
	 * @param string $name Table name.
	 *
	 * @return \Glue\DB\Table
	 */
	public function table($name) {
		if( ! isset($this->tables[$name]))
			$this->tables[$name] = $this->table_from_cache($name);
		return $this->tables[$name];
	}

	/**
	 * Loads a table from the disk cache. If it isn't there already, creates
	 * a new cache entry for it.
	 *
	 * @param string $name Table name.
	 *
	 * @return \Glue\DB\Table
	 */
	protected function table_from_cache($name) {
		$path = 'db/tables/'  . $this->id . '/' . $name . '.tmp';
		if ( ! $table = \Glue\Core::get_cache_entry($path)) {
			$table = $this->table_from_db($name);
			\Glue\Core::create_cache_entry($path, $table);
		}
		return $table;
	}

	/**
	 * Returns table object built by database introspection.
	 *
	 * @param $name
	 *
	 * @return \Glue\DB\Table
	 */
	abstract protected function table_from_db($name);

	/**
	 * Compiles given fragment into an SQL string.
	 *
	 * @param \Glue\DB\Fragment $fragment
	 *
	 * @return string
	 */
	public function compile(\Glue\DB\Fragment $fragment) {
		// Branch to the right function depending on fragment type :
		if ($fragment instanceof \Glue\DB\Fragment_Value)
			return $this->compile_value($fragment);
		elseif ($fragment instanceof \Glue\DB\Fragment_SQL)
			return $this->compile_sql($fragment);
		elseif ($fragment instanceof \Glue\DB\Fragment_Table)
			return $this->compile_table($fragment);
		elseif ($fragment instanceof \Glue\DB\Fragment_Item) {
			if ($fragment instanceof \Glue\DB\Fragment_Item_Bool)
				return $this->compile_item_bool($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_Join)
				return $this->compile_item_join($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_Orderby)
				return $this->compile_item_orderby($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_Groupby)
				return $this->compile_item_groupby($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_SelectList)
				return $this->compile_item_selectlist($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_UpdateList)
				return $this->compile_item_updatelist($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_Values)
				return $this->compile_item_values($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Item_InsertList)
				return $this->compile_item_insertlist($fragment);
		}
		elseif ($fragment instanceof \Glue\DB\Fragment_Builder) {
			if ($fragment instanceof \Glue\DB\Fragment_Builder_Bool)
				return $this->compile_builder_bool($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_Join)
				return $this->compile_builder_join($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_Orderby)
				return $this->compile_builder_orderby($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_Groupby)
				return $this->compile_builder_groupby($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_SelectList)
				return $this->compile_builder_selectlist($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_UpdateList)
				return $this->compile_builder_updatelist($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_Values)
				return $this->compile_builder_values($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Builder_InsertList)
				return $this->compile_builder_insertlist($fragment);
		}
		elseif ($fragment instanceof \Glue\DB\Fragment_Query) {
			if ($fragment instanceof \Glue\DB\Fragment_Query_Select)
				return $this->compile_query_select($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Query_Delete)
				return $this->compile_query_delete($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Query_Update)
				return $this->compile_query_update($fragment);
			elseif ($fragment instanceof \Glue\DB\Fragment_Query_Insert)
				return $this->compile_query_insert($fragment);
		}

		// No suitable function to compile fragment : throw exception
		throw new \Glue\DB\Exception("Cannot compile fragment of class '" . get_class($fragment) . "' : unknown fragment type.");
	}

	/* ***************************************************************************************************** */
	/* *********************************** FRAGMENT COMPILER FUNCTIONS ************************************* */
	/* ***************************************************************************************************** */

	/**
	 * Compiles Fragment_Value fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Value $fragment
	 *
	 * @return string
	 */
	protected function compile_value(\Glue\DB\Fragment_Value $fragment) {
		// Get data from fragment :
		$value = $fragment->value();

		// Generate SQL :
		return $this->quote_value($value);
	}

	/**
	 * Compiles Fragment_SQL fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_SQL $fragment
	 *
	 * @return string
	 */
	protected function compile_sql(\Glue\DB\Fragment_SQL $fragment) {
		// Get data from fragment :
		$template		= $fragment->sql();
		$replacements	= $fragment->replacements();

		// Split template according to inline string litterals and identifiers :
		$matches = preg_split("/('(?:''|[^'])*'|`(?:``|[^`])*`)/", $template, -1, PREG_SPLIT_DELIM_CAPTURE);

		// Loop over matches and generate SQL :
		$cn = $this;
		$sql = '';
		for($i = 0; $i < count($matches); $i++) {
			// Get string :
			$part = $matches[$i];

			// Tell apart delimiters from pieces :
			if ($i % 2 === 0) {
				// In-between string, we must make replacements :
				$sql .= preg_replace_callback(
					'/[?!]/',
					function ($matches) use ($cn, &$replacements) {
						// Get next replacement :
						$replacement = array_shift($replacements);

						// Replacement is a fragment ?
						if ($replacement instanceof \Glue\DB\Fragment)
							return $cn->compile($replacement);
						else {
							// Tell appart value from identifier replacements :
							if ($matches[0] === '?') {
								// Value :
								return $cn->quote_value($replacement);
							}
							else {
								// Identifier :
								if (is_array($replacement)) {
									$replacement = array_map(array($cn, 'quote_identifier'), $replacement);
									return implode('.', $replacement);
								}
								else
									return $cn->quote_identifier($replacement);
							}
						}
					},
					$part
				);
			}
			else {
				// Delimiter string, we must quote it according to current connection conventions :
				if ($part[0] === "'")	// String litteral :
					$sql .= $this->quote(\Glue\DB\DB::unquote($part));
				else					// Identifier :
					$sql .= $this->quote_identifier(\Glue\DB\DB::unquote_identifier($part));
			}
		}

		return $sql;
	}

	/**
	 * Compiles Fragment_Table fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Table $fragment
	 *
	 * @return string
	 */
	protected function compile_table(\Glue\DB\Fragment_Table $fragment) {
		// Get data from fragment :
		$table	= $fragment->table();
		$alias	= $fragment->alias();

		// Generate fragment SQL :
		$sql = $this->quote_identifier($table);
		if ( ! empty($alias))
			$sql .= ' AS ' . $this->quote_identifier($alias);

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Item_Bool fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_Bool $fragment
	 *
	 * @return string
	 */
	protected function compile_item_bool(\Glue\DB\Fragment_Item_Bool $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case \Glue\DB\DB::_AND :	$sql = 'AND ';	break;
				case \Glue\DB\DB::_OR :		$sql = 'OR ';	break;
			}
		}

		// Operand :
		$sql .= '(' . $this->compile($operand) . ')';

		return $sql;
	}

	/**
	 * Compiles Fragment_Item_Join fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_Join $fragment
	 *
	 * @return string
	 */
	protected function compile_item_join(\Glue\DB\Fragment_Item_Join $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();
		$on			= $fragment->on();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case \Glue\DB\DB::INNER :	$sql .= 'INNER JOIN ';			break;
				case \Glue\DB\DB::RIGHT :	$sql .= 'RIGHT OUTER JOIN ';	break;
				case \Glue\DB\DB::LEFT :	$sql .= 'LEFT OUTER JOIN ';		break;
				case \Glue\DB\DB::COMMA :	$sql .= ', ';					break;
			}
		}

		// Add operand SQL :
		$sqlop = $this->compile($operand);
		if ( ! $operand instanceof \Glue\DB\Fragment_Table)
			$sqlop	= '(' . $sqlop . ')';
		$sql .= $sqlop;

		// Add on SQL :
		if (isset($operator)) {
			$sqlon = $this->compile($on);
			$sql .= ' ON ' . $sqlon;
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Item_Orderby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_Orderby $fragment
	 *
	 * @return string
	 */
	protected function compile_item_orderby(\Glue\DB\Fragment_Item_Orderby $fragment) {
		// Get data from fragment :
		$ordered	= $fragment->ordered();
		$order		= $fragment->order();

		// Generate fragment SQL :
		$sql = '(' . $this->compile($ordered) . ')';

		// Add ordering :
		if (isset($order)) {
			switch ($order) {
				case \Glue\DB\DB::ASC :		$sql .= ' ASC';		break;
				case \Glue\DB\DB::DESC :	$sql .= ' DESC';	break;
				default : throw new \Glue\DB\Exception("Unknown order constant : " . $order);
			}
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Item_Groupby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_Groupby $fragment
	 *
	 * @return string
	 */
	protected function compile_item_groupby(\Glue\DB\Fragment_Item_Groupby $fragment) {
		// Get data from fragment :
		$grouped = $fragment->grouped();

		// Generate fragment SQL :
		$sql = '(' . $this->compile($grouped) . ')';

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Item_SelectList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_SelectList $fragment
	 *
	 * @return string
	 */
	protected function compile_item_selectlist(\Glue\DB\Fragment_Item_SelectList $fragment) {
		// Get data from fragment :
		$selected	= $fragment->selected();
		$alias		= $fragment->alias();

		// Generate fragment SQL :
		$sql = '(' . $this->compile($selected) . ')';

		// Add alias :
		if ( ! empty($alias))
			$sql .= ' AS ' . $this->quote_identifier($alias);

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Item_UpdateList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_UpdateList $fragment
	 *
	 * @return string
	 */
	protected function compile_item_updatelist(\Glue\DB\Fragment_Item_UpdateList $fragment) {
		// Get data from fragment :
		$setsql	= $this->quote_identifier($fragment->set());
		$tosql	= $this->compile($fragment->to());

		return $setsql . ' = (' . $tosql . ')';
	}

	/**
	 * Compiles Fragment_Item_InsertList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_InsertList $fragment
	 *
	 * @return string
	 */
	protected function compile_item_insertlist(\Glue\DB\Fragment_Item_InsertList $fragment) {
		return $this->quote_identifier($fragment->column());
	}

	/**
	 * Compiles Fragment_Item_Values fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Item_Values $fragment
	 *
	 * @return string
	 */
	protected function compile_item_values(\Glue\DB\Fragment_Item_Values $fragment) {
		// Get data from fragment :
		$values = $fragment->values();

		// Generate sql :
		$sql = array();
		foreach($values as $value)
			$sql[] = $value instanceof \Glue\DB\Fragment ? $this->compile($value) : $this->quote_value($value);
		$sql = '(' . implode(',', $sql) . ')';

		return $sql;
	}

	/**
	 * Compiles Fragment_Builder fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder $fragment
	 * @param string $connector
	 *
	 * @return string
	 */
	protected function compile_builder(\Glue\DB\Fragment_Builder $fragment, $connector) {
		// Get data from fragment :
		$children = $fragment->children();

		// Generate children fragment SQL strings :
		$sqls = array();
		foreach ($children as $child)
			$sqls[] = $this->compile($child);

		// Return SQL :
		return implode($connector, $sqls);
	}


	/**
	 * Compiles Fragment_Builder_Bool fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Bool $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_bool(\Glue\DB\Fragment_Builder_Bool $fragment) {
		$sql = $this->compile_builder($fragment, ' ');
		return $fragment->negated() ? 'NOT (' . $sql . ')' : $sql;
	}

	/**
	 * Compiles Fragment_Builder_Orderby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Orderby $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_orderby(\Glue\DB\Fragment_Builder_Orderby $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_SelectList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_SelectList $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_selectlist(\Glue\DB\Fragment_Builder_SelectList $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Groupby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Groupby $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_groupby(\Glue\DB\Fragment_Builder_Groupby $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Join fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Join $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_join(\Glue\DB\Fragment_Builder_Join $fragment) {
		return $this->compile_builder($fragment, ' ');
	}

	/**
	 * Compiles Fragment_Builder_UpdateList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_UpdateList $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_updatelist(\Glue\DB\Fragment_Builder_UpdateList $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Values fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Values $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_values(\Glue\DB\Fragment_Builder_Values $fragment) {
		return $this->compile_builder($fragment, ',');
	}

	/**
	 * Compiles Fragment_Builder_InsertList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_InsertList $fragment
	 *
	 * @return string
	 */
	protected function compile_builder_insertlist(\Glue\DB\Fragment_Builder_InsertList $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Query_Select fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Query_Select $fragment
	 *
	 * @return string
	 */
	protected function compile_query_select(\Glue\DB\Fragment_Query_Select $fragment) {
		// Get data from fragment :
		$selectsql	= $this->compile($fragment->columns());
		$fromsql	= $this->compile($fragment->from());
		$wheresql	= $this->compile($fragment->where());
		$groupbysql	= $this->compile($fragment->groupby());
		$havingsql	= $this->compile($fragment->having());
		$orderbysql	= $this->compile($fragment->orderby());
		$unique		= $fragment->unique();
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'SELECT ' . ($unique ? 'DISTINCT ' : '') . (empty($selectsql) ? '*' : $selectsql) . ' FROM ' . $fromsql;

		// Optional :
		if ( ! empty($wheresql))	$sql .= ' WHERE '		. $wheresql;
		if ( ! empty($groupbysql))	$sql .= ' GROUP BY '	. $groupbysql;
		if ( ! empty($havingsql))	$sql .= ' HAVING '		. $havingsql;
		if ( ! empty($orderbysql))	$sql .= ' ORDER BY '	. $orderbysql;
		if (   isset($limit))		$sql .= ' LIMIT '		. $limit;
		if (   isset($offset))		$sql .= ' OFFSET '		. $offset;

		return $sql;
	}

	/**
	 * Compiles Fragment_Query_Delete fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Query_Delete $fragment
	 *
	 * @return string
	 */
	protected function compile_query_delete(\Glue\DB\Fragment_Query_Delete $fragment) {
		// Get data from fragment :
		$fromsql	= $this->compile($fragment->table());
		$wheresql	= $this->compile($fragment->where());
		$orderbysql	= $this->compile($fragment->orderby());
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'DELETE FROM ' . $fromsql;

		// Optional :
		if ( ! empty($wheresql))	$sql .= ' WHERE '	. $wheresql;
		if ( ! empty($orderbysql))	$sql .= ' ORDER BY '. $orderbysql;
		if (   isset($limit))		$sql .= ' LIMIT '	. $limit;
		if (   isset($offset))		$sql .= ' OFFSET '	. $offset;

		return $sql;
	}

	/**
	 * Compiles Fragment_Query_Update fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Query_Update $fragment
	 *
	 * @return string
	 */
	protected function compile_query_update(\Glue\DB\Fragment_Query_Update $fragment) {
		// Get data from fragment :
		$setsql		= $this->compile($fragment->set());
		$tablesql	= $this->compile($fragment->table());
		$wheresql	= $this->compile($fragment->where());
		$orderbysql	= $this->compile($fragment->orderby());
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'UPDATE ' . $tablesql . ' SET ' . $setsql;

		// Optional :
		if ( ! empty($wheresql))	$sql .= ' WHERE '		. $wheresql;
		if ( ! empty($orderbysql))	$sql .= ' ORDER BY '	. $orderbysql;
		if (   isset($limit))		$sql .= ' LIMIT '		. $limit;
		if (   isset($offset))		$sql .= ' OFFSET '		. $offset;

		return $sql;
	}

	/**
	 * Compiles Fragment_Query_Insert fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Query_Insert $fragment
	 *
	 * @return string
	 */
	protected function compile_query_insert(\Glue\DB\Fragment_Query_Insert $fragment) {
		// Get data from fragment :
		$tablesql	= $this->compile($fragment->table());
		$valuessql	= $this->compile($fragment->values());
		$columnssql	= $this->compile($fragment->columns());

		// Generate SQL :
		$sql = 'INSERT INTO ' . $tablesql .
				(empty($columnssql) ? '' : ' (' . $columnssql . ')') .
				' VALUES ' . $valuessql;

		return $sql;
	}

	/**
	 * Quotes an identifier according to current connection conventions.
	 *
	 * @param string $identifier
	 *
	 * @return string
	 */
	public function quote_identifier($identifier) {
		$identifier = strtr($identifier, array('"' => '""'));
		return '"' . $identifier . '"';
	}

	/**
	 * Quotes a PHP value for inclusion into an SQL query. You may override this.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function quote_value($value) {
		if (is_string($value))
			return $this->quote_string($value);
		elseif (is_array($value))
			return $this->quote_array($value);
		elseif (is_bool($value))
			return $this->quote_bool($value);
		elseif (is_integer($value))
			return $this->quote_integer($value);
		elseif (is_float($value))
			return $this->quote_float($value);
		elseif (is_null($value))
			return $this->quote_null($value);
		else
			throw new \Glue\DB\Exception("Cannot quote given data type.");
	}

	/**
	 * Quotes a string for inclusion into an SQL query.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function quote_string($value) {
		return $this->quote($value);
	}

	/**
	 * Quotes an array for inclusion into an SQL query.
	 *
	 * @param array $value
	 *
	 * @return string
	 */
	protected function quote_array(array $value) {
		$arr = array();
		foreach ($value as $val)
			$arr[] = $this->quote_value($val);
		return '(' . implode(',', $arr) . ')';
	}

	/**
	 * Quotes an integer for inclusion into an SQL query.
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	protected function quote_integer($value) {
		return (string) $value;
	}

	/**
	 * Quotes an boolean for inclusion into an SQL query.
	 *
	 * @param boolean $value
	 *
	 * @return string
	 */
	protected function quote_bool($value) {
		return $value ? 'TRUE' : 'FALSE';
	}

	/**
	 * Quotes a float for inclusion into an SQL query.
	 *
	 * @param float $value
	 *
	 * @return string
	 */
	protected function quote_float($value) {
		return (string) $value;
	}

	/**
	 * Returns SQL representation of null.
	 *
	 * @param null $value
	 *
	 * @return string
	 */
	protected function quote_null($value) {
		return 'NULL';
	}

	/**
	 * Returns most appropriate PHP type to represent values from columns of given database type.
	 *
	 * @param string $dbtype
	 *
	 * @return string
	 */
	abstract public function phptype($dbtype);
}