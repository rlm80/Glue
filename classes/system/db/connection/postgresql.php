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
	 * @var string The hostname on which the database server resides.
	 */
	protected $host;

	/**
	 * @var string The port number where the database server is listening.
	 */
	protected $port;

	/**
	 * Builds DSN once all properties have been set.
	 */
	protected function dsn() {
		return 'pgsql:' .
			   'host=' . $this->host . ';' .
			   (isset($this->port) ? 'port=' . $this->port . ';' : '') .
			   (isset($this->dbname) ?	'dbname=' .	$this->dbname	. ';' : '');
	}

	/**
	 * Connection data initialization function.
	 */
	protected function init() {
		parent::init();
		if ( ! isset($this->dbname))	$this->dbname	= $this->default_dbname();
		if ( ! isset($this->host))		$this->host		= $this->default_host();
		if ( ! isset($this->port))		$this->port		= $this->default_port();
	}

	/**
	 * Default dbname.
	 */
	protected function default_dbname() {
		return null;
	}

	/**
	 * Default host.
	 */
	protected function default_host() {
		return 'localhost';
	}

	/**
	 * Default port.
	 */
	protected function default_port() {
		return null;
	}

	/**
	 * Returns table information by database introspection.
	 *
	 * @return array
	 */
	public function _intro_table($name) {
		throw new \Glue\DB\Exception("The Connection::intro_table function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
	}

	/**
	 * Retruns table list by database introspection as an array of table names indexed by table name.
	 *
	 * @return array
	 */
	protected function db_table_list() {
		throw new \Glue\DB\Exception("The Connection::db_table_list function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
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