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
	 * Returns structured information about the columns and primary key of a real database table.
	 * Columns are returned alphabetically ordered. Returns FALSE if table doesn't exist in database.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function table_info($name) {
		throw new \Glue\DB\Exception("The Connection::table_info function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
	}

	/**
	 * Returns all tables present in current database as an array of table names.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
	 *
	 * @return array Array of table names, numerically indexed, alphabetically ordered.
	 */
	public function real_tables() {
		throw new \Glue\DB\Exception("The Connection::real_tables function isn't implemeted for sqlite. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.sqlite.org/pragma.html");
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * @param \Glue\DB\Column $column
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function get_formatter(\Glue\DB\Column $column)  {
		throw new \Glue\DB\Exception("The Connection::get_phptype function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it.");
	}
}