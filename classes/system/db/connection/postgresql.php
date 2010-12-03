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
	 * Loads a table by database introspection.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Table
	 */
	abstract protected function create_table($name)
		throw new \Glue\DB\Exception("The Connection::create_table function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
	}

	/**
	 * Loads table list by database introspection.
	 *
	 * @return array
	 */
	public function create_table_list() {
		throw new \Glue\DB\Exception("The Connection::create_table_list function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
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