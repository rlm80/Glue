<?php

namespace Glue\System\DB;

/**
 * Main GlueDB class.
 *
 * Contains only static methods. Whatever you do with GlueDB, this should always be your entry point.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class DB {
	/**
	 * Returns a connection that is a singleton instance of the database class identified by $id
	 * in the connections array of \Glue\DB\Config. Calling the function with no parameter returns
	 * the default connection, which is a singleton instance of the first database class to appear
	 * in the connections array.
	 *
	 * @param string $id
	 *
	 * @return \Glue\DB\Database
	 */
	public static function db($id = null) {
		// No connection identifier given means first element in the connection array :
		if ( ! isset($id)) {
			$connections = \Glue\DB\Config::connections();
			list($id, ) = each($connections);
		}
		return \Glue\DB\Database::get($id);
	}

	/**
	 * Returns the virtual table identified by $table_name.
	 *
	 * Subsequent calls to this function with the same parameter will return the same
	 * virtual table instance, instead of creating a new one.
	 *
	 * @param string $table_name
	 *
	 * @return \Glue\DB\Table
	 */
	public static function table($table_name) {
		return \Glue\DB\Table::get($table_name);
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
	 * @param string $table_name
	 * @param string $alias
	 *
	 * @return \Glue\DB\Fragment_Table
	 */
	public static function alias($table_name, $alias = null) {  // TODO : would it be possible to rename this "table" and define
																// a toString method in the table fragments ? so that the aliasing
																// system may still be used with custom queries
		return new \Glue\DB\Fragment_Aliased_Table($table_name, $alias);
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
	 * @param string $table_name
	 * @param string $alias
	 *
	 * @return mixed
	 */
	public static function join($table_name = null, &$alias = null) {
		$f = new \Glue\DB\Fragment_Builder_Join();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $f->init($table_name, $alias);
		}
		else
			return $f;
	}

	/**
	 * We need to define this so that PHP doesn't think the function db() is the constructor.
	 */
	private function __construct() {}
}