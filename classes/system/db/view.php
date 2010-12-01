<?php

namespace Glue\System\DB;

/**
 * Base virtual table class.
 *
 * The tables you are referring to when you work with the query builder or with the
 * introspection API are not real database tables. They are PHP objects called virtual
 * tables. Just like real tables, virtual tables have names and columns. By default,
 * all virtual tables map to the corresponding table in the underlying database and
 * have the same columns so you actually don't notice that this system even exists at all.
 *
 * But you may define your own virtual tables. You do so by creating a class called
 * Table_<virtual table name> that extends Table.
 *
 * You may want to do that if you want to :
 * - have a virtual table point to a real table that has a different name,
 * - have a virtual table column point to a real column that has a different name,
 * - set up a Formatter for a column, other than the default one that simply
 *   type cast the values coming from the database according to the underlying column type.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Table {
	/**
	 * @var array Virtual tables instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string Name of this virtual table, as it will be refered to in the query builder.
	 */
	protected $name;

	/**
	 * @var string	Name of the database that owns the real underlying table.
	 */
	protected $dbname;

	/**
	 * @var string	Real underlying table name.
	 */
	protected $dbtable;

	/**
	 * @var array Primary key columns of this table.
	 */
	protected $pk;

	/**
	 * @var array Columns of this table.
	 */
	protected $columns;

	/**
	 * Constructor.
	 *
	 * @param string $name Table name.
	 */
	protected function __construct($name) {
		// Init name :
		$this->name = $name;

		// Init properties :
		if ( ! isset($this->dbtable))	$this->dbtable	= $this->init_dbtable();
		if ( ! isset($this->dbname))	$this->dbname	= $this->init_dbname();

		// Create columns :
		$this->columns = $this->init_columns();

		// Create pk :
		$this->pk = $this->init_pk();
	}

	/**
	 * Returns the name of the real underlying table.
	 *
	 * @return array
	 */
	protected function init_dbtable() {
		return $this->name;
	}

	/**
	 * Returns the name of the database that owns the real underlying table.
	 *
	 * @return string
	 */
	protected function init_dbname() {
		return 'default'; // TODO Do something better than this. We should look into each
										   // available database and search for one that owns the real table.
	}

	/**
	 * TODO
	 *
	 * @return string
	 */
	protected function init_pk() {
		return array();
	}

	/**
	 * Generates the columns by database introspection.
	 *
	 * This function makes use of get_column_alias() and get_column_formatter() to do
	 * the job. These functions are the ones that you may want to redefine, you
	 * shouldn't have to redefine this one.
	 *
	 * @return array
	 */
	private function init_columns() {
		$columns = array();
		$info_table = $this->db()->table_info($this->dbtable);
		foreach ($info_table['columns'] as $info_column) {
			// Create column object :
			$column = new \Glue\DB\Column(
					$this,
					$info_column['column'],
					$info_column['type'],
					$info_column['nullable'],
					$info_column['maxlength'],
					$info_column['precision'],
					$info_column['scale'],
					$info_column['default'],
					$info_column['auto']
				);

			// Add columns :
			$columns[$column->name()] = $column;
		}
		return $columns;
	}

	/**
	 * Returns the alias under which a real column will be known in PHP-land.
	 *
	 * This alias defines how you may refer to the column in the query builder. You
	 * may redefine this if, for example, you wish to change the name of a real column
	 * without impacting the PHP application, or the other way around.
	 *
	 * @param \Glue\DB\Column $column
	 *
	 * @return string
	 */
	public function get_column_alias(\Glue\DB\Column $column) {
		return $column->dbcolumn();
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * You may want to redefine this if, for example, it's not possible for GlueDB to
	 * guess the right PHP type from the db type (sqlite ?) or because you want some
	 * funky formatting like serialization.
	 *
	 * @param \Glue\DB\Column $column
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function get_column_formatter(\Glue\DB\Column $column) {
		return $this->db()->get_formatter($column);
	}

	/**
	 * Returns the name of this virtual table.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Returns the database object this virtual table is stored into.
	 *
	 * @return \Glue\DB\Connection
	 */
	public function db() {
		return \Glue\DB\DB::cn($this->dbname);
	}

	/**
	 * Returns the primary key columns of this table.
	 *
	 * @return array
	 */
	public function pk() {
		return $this->pk;
	}

	/**
	 * Returns the database name this virtual table is stored into.
	 *
	 * @return string
	 */
	public function dbname() {
		return $this->dbname;
	}

	/**
	 * Returns the real underlying table name.
	 *
	 * @return string
	 */
	public function dbtable() {
		return $this->dbtable;
	}

	/**
	 * Returns the columns of this table.
	 *
	 * @return array
	 */
	public function columns() {
		return $this->columns;
	}

	/**
	 * Returns a column.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Column
	 */
	public function column($name) {
		if ( ! isset($this->columns[$name]))
			throw new \Glue\DB\Exception("There is no column " . $name . " in table " . $this->name . ".");
		return $this->columns[$name];
	}

	/**
	 * Whether or not the column is defined.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function column_exists($name) {
		return isset($this->columns[$name]);
	}

	/**
	 * Loads a virtual table, stores it in cache, and returns it.
	 *
	 * @param string $name Virtual table name.
	 *
	 * @return \Glue\DB\Table
	 */
	static public function get($name) {
		$name = strtolower($name);
		if( ! isset(self::$instances[$name]))
			self::$instances[$name] = self::create_from_cache($name);
		return self::$instances[$name];
	}

	/**
	 * Loads a virtual table from the disk cache. If it isn't there already, creates
	 * a new cache entry for it.
	 *
	 * @param string $name Virtual table name.
	 *
	 * @return \Glue\DB\Table
	 */
	static protected function create_from_cache($name) {
		// Look up object into cache directory :
		$dir	= \Glue\ROOTPATH . "cache/db/tables/";
		$path	= $dir . $name . ".tmp";

		// Check cache availability :
		if ( ! file_exists($path)) {
			$table = self::create_from_class($name);
			if ( ! is_dir($dir)) mkdir($dir, 777, true);
			file_put_contents($path, serialize($table));
		}

		// Return table from cache :
		return unserialize(file_get_contents($path));
	}


	/**
	 * Loads a virtual table by instanciating the appropriate class.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Table
	 */
	static protected function create_from_class($name) {
		$class = 'Glue\\DB\\Table_' . ucfirst($name);
		if (class_exists($class))
			return new $class($name);
		else
			return new \Glue\DB\Table($name);
	}
}
