<?php

namespace Glue\System\DB;

/**
 * Base MySQL connection class.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Connection_MySQL extends \Glue\DB\Connection {
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
		return 'mysql:' .
			   'host=' . $this->host . ';' .
			   (isset($this->port) ? 	'port=' .	$this->port		. ';' : '') .
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
	 * Returns table object built by database introspection.
	 *
	 * @param $name
	 *
	 * @return \Glue\DB\Table
	 */
	protected function table_from_db($name) {
		// Query information schema to get columns information :
		$stmt = $this->prepare("
			SELECT
				column_name,
				data_type,
				is_nullable,
				column_default,
				character_maximum_length,
				numeric_precision,
				numeric_scale,
				extra,
				ordinal_position
			FROM
				information_schema.columns
			WHERE
				table_schema = :dbname AND
				table_name = :tablename
			ORDER BY
				ordinal_position
		");
		$stmt->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create columns :
		$columns = array();
		foreach($stmt as $row) {
			// Column name and type :
			$colname = trim(strtolower($row[0]));
			$coltype = trim(strtolower($row[1]));

			// Build object :
			$column = new \Glue\DB\Column(
				$this->id,
				$name,
				$colname,
				$coltype,
				($row[2] === 'YES' ? true : false),
				isset($row[4]) ? (integer) $row[4] : null,
				isset($row[5]) ? (integer) $row[5] : null,
				isset($row[6]) ? (integer) $row[6] : null,
				$row[3],
				trim(strtolower($row[7])) === 'auto_increment' ? true : false,
				(integer) $row[8],
				$this->phptype($coltype)
			);

			// Add column to array :
			$columns[$colname] = $column;
		}

		// No columns ? Means table didn't exist :
		if (count($columns) === 0)
			throw new \Glue\DB\Exception("Table " . $name . " not found on connection " . $this->id . ".");

		// Query information schema to get pk information :
		$stmt = $this->prepare("
			SELECT
				column_name
			FROM
				information_schema.statistics
			WHERE
				table_schema = :dbname AND
				table_name = :tablename AND
				index_name = 'PRIMARY'
			ORDER BY
				seq_in_index
		");
		$stmt->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create pk :
		$pk = array();
		foreach($stmt as $row) {
			// Column name :
			$colname = $row[0];

			// Add column to pk :
			$pk[$colname] = $columns[$colname];
		}

		// Create and return info :
		return new \Glue\DB\Table($this->id, $name, $columns, $pk);
	}

	/**
	 * Retruns table list by database introspection as an array of table names indexed by table name.
	 *
	 * @return array
	 */
	protected function table_list_from_db() {
		$stmt = $this->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname");
		$stmt->execute(array(':dbname' => $this->dbname));
		$tables = array();
		while ($table = $stmt->fetchColumn()) {
			$name = strtolower($table);
			$tables[$name] = $name;
		}
		return $tables;
	}

	/**
	 * Returns most appropriate PHP type to represent values from columns of given database type.
	 *
	 * @param string $dbtype
	 *
	 * @return string
	 */
	public function phptype($dbtype) {
		// Extract first word from type (MySQL may return things like "float unsigned" sometimes) :
		if (preg_match('/^\S+/', $dbtype, $matches))
			$dbtype = $matches[0];

		// Convert type to upper case :
		$dbtype = strtoupper($dbtype);

		// Compute appropriate php type :
		switch ($dbtype) {
			// Integer types :
			case 'TINYINT'; case 'SMALLINT'; case 'MEDIUMINT'; case 'INT'; case 'BIGINT';
				$phptype = 'integer';
				break;

			// Real types :
			case 'FLOAT'; case 'DOUBLE'; case 'DECIMAL';
				$phptype = 'float';
				break;

			// Boolean types :
			case 'BIT';
				$phptype = 'boolean';
				break;

			// String types :
			case 'CHAR'; case 'VARCHAR'; case 'TINYTEXT'; case 'TEXT';
			case 'MEDIUMTEXT'; case 'LONGTEXT'; case 'ENUM'; case 'SET';
				$phptype = 'string';
				break;

			// Binary types :
			case 'BINARY'; case 'VARBINARY'; case 'TINYBLOB'; case 'BLOB';
			case 'MEDIUMBLOB'; case 'LONGBLOB';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Date and time types :
			case 'DATE'; case 'DATETIME'; case 'TIME'; case 'TIMESTAMP'; case 'YEAR';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Default :
			default;
				throw new \Glue\DB\Exception("Unknown MySQL data type : " . $dbtype);
		}

		return $phptype;
	}

	/**
	 * Quotes an identifier according to MySQL conventions. Mysql uses back-ticks for this
	 * instead of the ANSI double quote standard character.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		$identifier = strtr($identifier, array('`' => '``'));
		return '`' . $identifier . '`';
	}
}