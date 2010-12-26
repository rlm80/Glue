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
	 * Returns all the tables defined on this connection as an array indexed by table alias.
	 *
	 * @return array
	 */
	public function tables() {
		$tables	= array();
		$list	= $this->table_list();
		foreach ($list as $name)
			$tables[$name] = $this->table($name);
		return $tables;
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
			$list = $this->create_table_list();
			\Glue\Core::create_cache_entry($path, $list);
		}
		return $list;
	}

	/**
	 * Creates table list from scratch and returns it.
	 *
	 * @return array
	 */
	protected function create_table_list() {
		// Get table names :
		$tables = $this->db_table_list();

		// Get view names :
		$views	= array();
		$dir	= \Glue\CLASSPATH_USER . 'db/table';
		foreach(\Glue\Core::globr($dir , '*.php') as $file) {
			$parts = explode('/', substr($file, strlen($dir) + 1, -4));
			array_shift($parts);
			$name = implode('_', $parts);
			$views[$name] = $name;
		}

		return array_merge($tables, $views);
	}

	/**
	 * Retruns table list by database introspection as an array of table names indexed by table name.
	 *
	 * @return array
	 */
	abstract protected function db_table_list();

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
			$table = $this->create_table($name);
			\Glue\Core::create_cache_entry($path, $table);
		}
		return $table;
	}

	/**
	 * Loads a table by instanciating the appropriate class.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Table
	 */
	protected function create_table($name) {
		$class = 'Glue\\DB\\Table_' . ucfirst($this->id) . '_' . ucfirst($name);
		if (class_exists($class))
			return new $class($this->id, $name);
		else
			return new \Glue\DB\Table($this->id, $name);
	}

	/**
	 * Returns table information by database introspection.
	 *
	 * @param $name
	 *
	 * @return array
	 */
	abstract public function _intro_table($name);

	/**
	 * Returns the appropriate formatter for given db type.
	 *
	 * @param string $dbtype
	 *
	 * @return \Glue\DB\Formatter
	 */
	abstract public function get_formatter($dbtype);

	/* ***************************************************************************************************** */
	/* *********************************** FRAGMENT COMPILER FUNCTIONS ************************************* */
	/* ***************************************************************************************************** */

	/**
	 * Compiles Fragment_Operand_Bool fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Operand_Bool $fragment
	 *
	 * @return string
	 */
	public function compile_operand_bool(\Glue\DB\Fragment_Operand_Bool $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case \Glue\DB\Fragment_Operand_Bool::_AND :	$sql = 'AND ';		break;
				case \Glue\DB\Fragment_Operand_Bool::_OR :	$sql = 'OR ';		break;
			}
		}

		// Operand :
		$sql .= '(' . $operand->sql($this) . ')';

		return $sql;
	}

	/**
	 * Compiles Fragment_Operand_Join fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Operand_Join $fragment
	 *
	 * @return string
	 */
	public function compile_operand_join(\Glue\DB\Fragment_Operand_Join $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();
		$on			= $fragment->on();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case \Glue\DB\Fragment_Operand_Join::INNER_JOIN :		$sql .= 'INNER JOIN ';			break;
				case \Glue\DB\Fragment_Operand_Join::RIGHT_OUTER_JOIN :	$sql .= 'RIGHT OUTER JOIN ';	break;
				case \Glue\DB\Fragment_Operand_Join::LEFT_OUTER_JOIN :	$sql .= 'LEFT OUTER JOIN ';		break;
			}
		}

		// Add operand SQL :
		$sqlop = $operand->sql($this);
		if ( ! $operand instanceof \Glue\DB\Fragment_Aliased_Table)
			$sqlop	= '(' . $sqlop . ')';
		$sql .= $sqlop;

		// Add on SQL :
		if (isset($operator)) {
			$sqlon = $on->sql($this);
			$sql .= ' ON ' . $sqlon;
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Aliased fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Aliased $fragment
	 *
	 * @return string
	 */
	public function compile_aliased(\Glue\DB\Fragment_Aliased $fragment) {
		// Get data from fragment :
		$aliased	= $fragment->aliased();
		$as			= $fragment->as();

		// Generate fragment SQL :
		$sql = $aliased->sql($this);
		if ( ! ($aliased instanceof \Glue\DB\Fragment_Column || $aliased instanceof \Glue\DB\Fragment_Table))
			$sql	= '(' . $sql . ')';

		// Add alias :
		if ( ! empty($as))
			$sql .= ' AS ' . $this->quote_identifier($as);

		// Return SQL :
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
	public function compile_builder(\Glue\DB\Fragment_Builder $fragment, $connector) {
		// Get data from fragment :
		$children = $fragment->children();

		// Generate children fragment SQL strings :
		$sqls = array();
		foreach ($children as $child)
			$sqls[] = $child->sql($this);

		// Return SQL :
		return implode($connector, $sqls);
	}

	/**
	 * Compiles Fragment_Builder_SelectList fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_SelectList $fragment
	 *
	 * @return string
	 */
	public function compile_builder_selectlist(\Glue\DB\Fragment_Builder_SelectList $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Orderby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Orderby $fragment
	 *
	 * @return string
	 */
	public function compile_builder_orderby(\Glue\DB\Fragment_Builder_Orderby $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Groupby fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Groupby $fragment
	 *
	 * @return string
	 */
	public function compile_builder_groupby(\Glue\DB\Fragment_Builder_Groupby $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Bool fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Bool $fragment
	 *
	 * @return string
	 */
	public function compile_builder_bool(\Glue\DB\Fragment_Builder_Bool $fragment) {
		$sql = $this->compile_builder($fragment, ' ');
		return $fragment->is_negated() ? 'NOT (' . $sql . ')' : $sql;
	}

	/**
	 * Compiles Fragment_Builder_Bool_Where fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Bool_Where $fragment
	 *
	 * @return string
	 */
	public function compile_builder_bool_where(\Glue\DB\Fragment_Builder_Bool_Where $fragment) {
		return $this->compile_builder_bool($fragment, ' ');
	}

	/**
	 * Compiles Fragment_Builder_Bool_Having fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Bool_Having $fragment
	 *
	 * @return string
	 */
	public function compile_builder_bool_having(\Glue\DB\Fragment_Builder_Bool_Having $fragment) {
		return $this->compile_builder_bool($fragment, ' ');
	}

	/**
	 * Compiles Fragment_Builder_Join fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Join $fragment
	 *
	 * @return string
	 */
	public function compile_builder_join(\Glue\DB\Fragment_Builder_Join $fragment) {
		return $this->compile_builder($fragment, ' ');
	}

	/**
	 * Compiles Fragment_Builder_Join_From fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Join_From $fragment
	 *
	 * @return string
	 */
	public function compile_builder_join_from(\Glue\DB\Fragment_Builder_Join_From $fragment) {
		return $this->compile_builder($fragment, ' ');
	}

	/**
	 * Compiles Fragment_Builder_Setlist fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Setlist $fragment
	 *
	 * @return string
	 */
	public function compile_builder_setlist(\Glue\DB\Fragment_Builder_Setlist $fragment) {
		return $this->compile_builder($fragment, ', ');
	}

	/**
	 * Compiles Fragment_Builder_Rowlist fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Rowlist $fragment
	 *
	 * @return string
	 */
	public function compile_builder_rowlist(\Glue\DB\Fragment_Builder_Rowlist $fragment) {
		return $this->compile_builder($fragment, ',');
	}

	/**
	 * Compiles Fragment_Builder_Columns fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Builder_Columns $fragment
	 *
	 * @return string
	 */
	public function compile_builder_columns(\Glue\DB\Fragment_Builder_Columns $fragment) {
		// Get data from fragment :
		$children = $fragment->children();

		// Generate children fragment SQL strings :
		$sqls = array();
		foreach ($children as $child)
			$sqls[] = $child->sql($this, \Glue\DB\Fragment_Column::STYLE_UNQUALIFIED);

		// Return SQL :
		return implode(', ', $sqls);
	}

	/**
	 * Compiles Fragment_Ordered fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Ordered $fragment
	 *
	 * @return string
	 */
	public function compile_ordered(\Glue\DB\Fragment_Ordered $fragment) {
		// Get data from fragment :
		$ordered	= $fragment->ordered();
		$order		= $fragment->order();

		// Generate fragment SQL :
		$sql = $ordered->sql($this);
		if ( ! $ordered instanceof \Glue\DB\Fragment_Column)
			$sql	= '(' . $sql . ')';

		// Add ordering :
		if (isset($order)) {
			switch ($order) {
				case \Glue\DB\Fragment_Ordered::ASC :		$sql .= ' ASC';		break;
				case \Glue\DB\Fragment_Ordered::DESC :	$sql .= ' DESC';	break;
			}
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles Fragment_Column fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Column $fragment
	 * @param integer $style
	 *
	 * @return string
	 */
	public function compile_column(\Glue\DB\Fragment_Column $fragment, $style) {
		// Get column :
		$column = $fragment->column()->dbcolumn();

		// Generate SQL :
		if ($style === \Glue\DB\Fragment_Column::STYLE_UNQUALIFIED) {
			// Don't prepend table alias :
			$sql = $this->quote_identifier($column);
		}
		else {
			// Prepend table alias :
			$as = $fragment->table_alias()->as();
			if (empty($as))
				$as = $fragment->table_alias()->aliased()->table()->dbtable();
			$sql = $this->quote_identifier($as) . '.' . $this->quote_identifier($column);
		}

		return $sql;
	}

	/**
	 * Compiles Fragment_Column fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Table $fragment
	 *
	 * @return string
	 */
	public function compile_table(\Glue\DB\Fragment_Table $fragment) {
		$table = $this->table($fragment->table());
		return $this->quote_identifier($table->dbtable());
	}

	/**
	 * Compiles Fragment_Template fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Template $fragment
	 *
	 * @return string
	 */
	public function compile_template(\Glue\DB\Fragment_Template $fragment) {
		// Get data from fragment :
		$template		= $fragment->template();
		$replacements	= $fragment->replacements();

		// Break appart template :
		$parts = explode('?', $template);
		if (count($parts) !== count($replacements) + 1)
			throw new \Glue\DB\Exception("Number of placeholders different from number of replacements for " . $template);

		// Make replacements :
		$max = count($replacements);
		$sql = $parts[0];
		for($i = 0; $i < $max; $i++) {
			$sql .= $replacements[$i]->sql($this);
			$sql .= $parts[$i + 1];
		}

		return $sql;
	}

	/**
	 * Compiles Fragment_Value fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Value $fragment
	 *
	 * @return string
	 */
	public function compile_value(\Glue\DB\Fragment_Value $fragment) {
		// Get data from fragment :
		$value = $fragment->value();

		// Generate SQL :
		return $this->quote_value($value);
	}

	/**
	 * Compiles Fragment_Query_Select fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Query_Select $fragment
	 *
	 * @return string
	 */
	public function compile_query_select(\Glue\DB\Fragment_Query_Select $fragment) {
		// Get data from fragment :
		$selectsql	= $fragment->columns()->sql($this);
		$fromsql	= $fragment->from()->sql($this);
		$wheresql	= $fragment->where()->sql($this);
		$groupbysql	= $fragment->groupby()->sql($this);
		$havingsql	= $fragment->having()->sql($this);
		$orderbysql	= $fragment->orderby()->sql($this);
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'SELECT ' . (empty($selectsql) ? '*' : $selectsql) . ' FROM ' . $fromsql;

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
	public function compile_query_delete(\Glue\DB\Fragment_Query_Delete $fragment) {
		// Get data from fragment :
		$fromsql	= $fragment->from()->sql($this);
		$wheresql	= $fragment->where()->sql($this);
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'DELETE FROM ' . $fromsql;

		// Optional :
		if ( ! empty($wheresql))	$sql .= ' WHERE '	. $wheresql;
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
	public function compile_query_update(\Glue\DB\Fragment_Query_Update $fragment) {
		// Get data from fragment :
		$setlistsql	= $fragment->set()->sql($this);
		$fromsql	= $fragment->from()->sql($this);
		$wheresql	= $fragment->where()->sql($this);
		$orderbysql	= $fragment->orderby()->sql($this);
		$limit		= $fragment->limit();
		$offset		= $fragment->offset();

		// Mandatory :
		$sql = 'UPDATE ' . $fromsql . ' SET ' . $setlistsql;

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
	public function compile_query_insert(\Glue\DB\Fragment_Query_Insert $fragment) {
		// Get data from fragment :
		$intosql	= $fragment->into()->sql($this);
		$valuessql	= $fragment->values()->sql($this);
		$columnssql	= $fragment->columns()->sql($this);

		// Generate SQL :
		$sql = 'INSERT INTO ' . $intosql .
				(empty($columnssql) ? '' : ' (' . $columnssql . ')') .
				' VALUES ' . $valuessql;

		return $sql;
	}

	/**
	 * Compiles Fragment_Assignment fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Assignment $fragment
	 *
	 * @return string
	 */
	public function compile_assignment(\Glue\DB\Fragment_Assignment $fragment) {
		// Get data from fragment :
		$columnsql	= $fragment->column()->sql($this);
		$tosql		= $fragment->to()->sql($this);

		return $columnsql . ' = ' . $tosql;
	}

	/**
	 * Compiles Fragment_Row fragments into an SQL string.
	 *
	 * @param \Glue\DB\Fragment_Row $fragment
	 *
	 * @return string
	 */
	public function compile_row(\Glue\DB\Fragment_Row $fragment) {
		// Get data from fragment :
		$values = $fragment->values();

		// Generate value fragments SQL strings :
		$sqls = array();
		foreach ($values as $value)
			$sqls[] = $value->sql($this);

		// Return SQL :
		return '(' . implode(',', $sqls) . ')';
	}

	/**
	 * Quotes an identifier according to current connection conventions.
	 *
	 * @param string $identifier
	 *
	 * @return string
	 */
	protected function quote_identifier($identifier) {
		return '"' . $identifier . '"';
	}

	/**
	 * Quotes a value for inclusion into an SQL query.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	protected function quote_value($value) {
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
			throw new \Glue\DB\Exception("Cannot quote objects.");
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
}