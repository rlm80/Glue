<?php

namespace Glue\System\DB;

/**
 * Base PostgreSQL connection class.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Connection_PostgreSQL extends \Glue\DB\Connection {
	/**
	 * @var string The name of the database. We must store this because introspection queries require it.
	 */
	protected $dbname;
		
	/**
	 * Constructor.
	 *
	 * @param $dbname The name of the database.
	 * @param $username Username used to connect to the database.
	 * @param $password Password used to connect to the database.
	 * @param $host The hostname on which the database server resides.
	 * @param $port The port number where the database server is listening.
	 * @param $options A key=>value array of driver-specific connection options.
	 * @param $charset Connection charset.
	 */
	public function __construct($dbname, $username, $password, $host = 'localhost', $port = null, $options = array(), $charset = 'utf8') {
		// Build DSN :
		$dsn = 'pgsql:' .
			   'host=' . $host . ';' .
			   (isset($port) ? 'port=' . $port . ';' : '') .
			   'dbname=' . $dbname . ';';

		// Store dbname :
		$this->dbname = $dbname;

		// Call parent constructor :
		parent::__construct($dsn, $username, $password, $options);
		
		// Set connection charset :
		$this->exec('SET NAMES ' . $this->quote($charset));
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
		throw new \Glue\DB\Exception("The Connection::table_info function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
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
		throw new \Glue\DB\Exception("The Connection::real_tables function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
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