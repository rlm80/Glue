<?php

namespace Glue\System\DB;

/**
 * Main DB class.
 *
 * Contains only static methods. Entry point for about everything you may want to do with this library.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class DB {
	// Order by constants :
	const ASC	= 0;
	const DESC	= 1;
		
	/**
	 * @var array Connection instances cache.
	 */
	static protected $connections = array();

	/**
	 * @var array Connection ids cache.
	 */
	static protected $connection_list;

	/**
	 * Default connection id.
	 *
	 * @return string
	 */
	static protected function default_connection_id() {
		return 'default';
	}

	/**
	 * Returns connection identified by given id.
	 *
	 * @param string $id
	 *
	 * @return \Glue\DB\Connection
	 */
	static public function cn($id = null) {
		// No id given ? Means default id :
		$id = static::default_connection_id();

		// Loads and returns connection :
		if( ! isset(static::$connections[$id]))
			static::$connections[$id] = static::create_connection($id);
		return static::$connections[$id];
	}

	/**
	 * Creates a new connection instance and returns it.
	 *
	 * @param string $id
	 *
	 * @return \Glue\DB\Connection
	 */
	static protected function create_connection($id) {
		$class = 'Glue\\DB\\Connection_' . ucfirst($id);
		return new $class($id);
	}

	/**
	 * Returns all defined connections as an array of connections indexed by connection id.
	 *
	 * @return array
	 */
	static public function connections() {
		$connections	= array();
		$list			= static::connection_list();
		foreach ($list as $id)
			$connections[$id] = static::cn($id);
		return $connections;
	}

	/**
	 * Whether or not the connection is defined.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	static public function connection_exists($id) {
		return array_key_exists($id, static::connection_list());
	}

	/**
	 * Returns an array with all defined connections, as an array of connection ids indexed
	 * by connection id.
	 *
	 * @return array
	 */
	static public function connection_list() {
		if( ! isset(static::$connection_list)) {
			static::$connection_list = array();
			$dir = \Glue\CLASSPATH_USER . 'db/connection';
			foreach(\Glue\Core::globr($dir , '*.php') as $file) {
				$id = strtolower(substr($file, strlen($dir) + 1, -4));
				static::$connection_list[$id] = $id;
			}
		}
		return static::$connection_list;
	}

	/**
	 * Returns a select query object.
	 *
	 * @param string $table_name Name of the main table you're selecting from (= first table in the from clause).
	 * @param \Glue\DB\Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public static function select($table_name = null, &$alias = null) {
		$f = new \Glue\DB\Fragment_Query_Select();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $f->from($table_name, $alias);
		}
		else
			return $f;
	}

	/**
	 * Returns an update query object.
	 *
	 * @param string $table_name Name of the main table you're updating (= first table in the update clause).
	 * @param \Glue\DB\Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public static function update($table_name, &$alias = null) {
		$query = new \Glue\DB\Fragment_Query_Update();
		$query->from($table_name, $alias);
		return $query->from();
	}

	/**
	 * Returns a delete query object.
	 *
	 * @param string $table_name Name of the table you're deleting from.
	 * @param \Glue\DB\Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public static function delete($table_name, &$alias = null) {
		return new \Glue\DB\Fragment_Query_Delete($table_name, $alias);
	}

	/**
	 * Returns a insert query object.
	 *
	 * @param string $table_name Name of the table you're inserting data into.
	 * @param \Glue\DB\Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return \Glue\DB\Fragment_Query_Insert
	 */
	public static function insert($table_name, &$alias = null) {
		return new \Glue\DB\Fragment_Query_Insert($table_name, $alias);
	}

	/**
	 * Returns a new value fragment.
	 *
	 * @param mixed $value
	 *
	 * @return \Glue\DB\Fragment_Value
	 */
	public static function value($value = null) {
		return new \Glue\DB\Fragment_Value($value);
	}

	/**
	 * Returns a new table fragment.
	 *
	 * @param string $table
	 * @param string $alias
	 *
	 * @return \Glue\DB\Fragment_Table
	 */
	public static function table($table, $alias = null) {
		return new \Glue\DB\Fragment_Table($table, $alias);
	}

	/**
	 * Returns a new template fragment.
	 *
	 * @return \Glue\DB\Fragment_Template
	 */
	public static function template() {
		$values		= func_get_args();
		$template	= array_shift($values);
		return new \Glue\DB\Fragment_Template($template, $values);
	}

	/**
	 * Returns a new boolean fragment.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public static function bool() {
		$f = new \Glue\DB\Fragment_Builder_Bool();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($f, 'init'), $args);
		}
		else
			return $f;
	}

	/**
	 * Returns a new join fragment.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	public static function join($table = 0, &$obj = null) {
		$f = new \Glue\DB\Fragment_Builder_Join();
		if (func_num_args() > 0)
			return $f->init($table, $alias);
		else
			return $f;
	}
}