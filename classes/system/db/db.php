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

	// Join operators :
	const LEFT	= 0;
	const RIGHT	= 1;
	const INNER	= 2;
	const COMMA	= 3;

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
	 * Returns a new select query object and forwards parameters to Fragment_Query_Select::from().
	 *
	 * @param mixed $table
	 * @param mixed $operand
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public static function select($table = null, &$operand = null) {
		$f = new \Glue\DB\Fragment_Query_Select();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $f->from($table, $operand);
		}
		else
			return $f;
	}

	/**
	 * Returns a new update query object and forwards parameters to Fragment_Query_Update::table().
	 *
	 * @param mixed $table
	 * @param mixed $operand
	 *
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public static function update($table, &$operand = null) {
		$query = new \Glue\DB\Fragment_Query_Update();
		return $query->table($table, $operand);
	}

	/**
	 * Returns a new update query object and forwards parameters to Fragment_Query_Delete::table().
	 *
	 * @param mixed $table
	 * @param mixed $operand
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public static function delete($table, &$operand = null) {
		$query = new \Glue\DB\Fragment_Query_Delete();
		return $query->table($table, $operand);
	}

	/**
	 * Returns a insert query object.
	 *
	 * @param mixed $table
	 *
	 * @return \Glue\DB\Fragment_Query_Insert
	 */
	public static function insert($table) {
		$query = new \Glue\DB\Fragment_Query_Insert();
		return $query->table($table);
	}

	/**
	 * Returns a new value fragment.
	 *
	 * @param mixed $value
	 *
	 * @return \Glue\DB\Fragment_Value
	 */
	public static function val($value = null) {
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
	public static function tpl() {
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

	/**
	 * Returns a new order by fragment.
	 *
	 * @return \Glue\DB\Fragment_Builder_Orderby
	 */
	public static function orderby() {
		$f = new \Glue\DB\Fragment_Builder_Orderby();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($f, 'orderby'), $args);
		}
		else
			return $f;
	}

	/**
	 * Returns a new group by fragment.
	 *
	 * @return \Glue\DB\Fragment_Builder_Groupby
	 */
	public static function groupby() {
		$f = new \Glue\DB\Fragment_Builder_Groupby();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($f, 'groupby'), $args);
		}
		else
			return $f;
	}

	/**
	 * Quotes a string literal for inclusion in a template. TODO rename this q() ?
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function quote($str) {
		return "'" . strtr($str, array("'" => "''")) . "'";
	}

	/**
	 * Takes a string literal quoted for inclusion in a template and returns the unquoted string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function unquote($str) {
		return strtr(substr($str, 1, -1), array("''" => "'"));
	}

	/**
	 * Quotes an identifier for inclusion in a template. TODO rename this qi() ?
	 *
	 * @param mixed $str Array or string.
	 *
	 * @return string
	 */
	public static function quote_identifier($str) {
		if (is_array($str))
			return implode('.', array_map('\Glue\DB\DB::quote_identifier', $str));
		else
			return "`" . strtr($str, array("`" => "``")) . "`";
	}

	/**
	 * Takes an identifier quoted for inclusion in a template and returns the unquoted string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function unquote_identifier($str) {
		return strtr(substr($str, 1, -1), array("``" => "`"));
	}
}