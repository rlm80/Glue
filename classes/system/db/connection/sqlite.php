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
	 * Constructor.
	 *
	 * @param $path
	 * @param $options A key=>value array of driver-specific connection options.
	 */
	public function __construct($path, $options = array()) {
		// Build DSN :
		$dsn = 'sqlite:' . $path;

		// Call parent constructor :
		parent::__construct($dsn, null, null, $options);
	}

	/**
	 * Loads a table by database introspection.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Table
	 */
	public function _intro_table($name) {
		throw new \Glue\DB\Exception("The Connection::intro_table function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
	}

	/**
	 * Returns table list by database introspection.
	 *
	 * @return array
	 */
	public function _intro_table_list() {
		throw new \Glue\DB\Exception("The Connection::intro_table_list function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
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