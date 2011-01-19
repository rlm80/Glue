<?php

namespace Glue\System\DB;

/**
 * Base Sqlite connection class.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Connection_SQLite extends \Glue\DB\Connection {
	/**
	 * @var string Path to sqlite file.
	 */
	protected $path;

	/**
	 * Builds DSN once all properties have been set.
	 */
	protected function dsn() {
		return 'sqlite:' . $this->path;
	}

	/**
	 * Connection data initialization function.
	 */
	protected function init() {
		parent::init();
		if ( ! isset($this->path)) $this->path = $this->default_path();
	}

	/**
	 * Default path.
	 */
	protected function default_path() {
		return null;
	}

	/**
	 * Returns table object built by database introspection.
	 *
	 * @param $name
	 *
	 * @return \Glue\DB\Table
	 */
	protected function table_from_db($name) {
		throw new \Glue\DB\Exception("The Connection::table_from_db function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
	}

	/**
	 * Retruns table list by database introspection as an array of table names indexed by table name.
	 *
	 * @return array
	 */
	protected function table_list_from_db() {
		throw new \Glue\DB\Exception("The Connection::table_list_from_db function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
	}

	/**
	 * Returns the appropriate formatter for given db type.
	 *
	 * @param string $dbtype
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function get_formatter($dbtype) {
		throw new \Glue\DB\Exception("The Connection::get_phptype function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it.");
	}
}