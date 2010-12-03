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
		$dsn = 'mysql:' .
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
	public function create_table($name) {
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
				extra
			FROM
				information_schema.columns
			WHERE
				table_schema = :dbname AND
				table_name = :tablename
		");
		$stmt->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create columns data structure :
		$columns = array();
		foreach($stmt as $row) {
			// Build array :
			$columns[] = array(
				'column'	=> trim(strtolower($row[0])),					// Column name
				'type'		=> trim(strtolower($row[1])),					// Native database type
				'nullable'	=> ($row[2] === 'YES' ? true : false),			// Whether or not the column is nullable
				'default'	=> $row[3],										// Maximum length of a text column
				'maxlength'	=> isset($row[4]) ? (integer) $row[4] : null,	// Precision of the column
				'precision' => isset($row[5]) ? (integer) $row[5] : null,	// Scale of the column
				'scale' 	=> isset($row[6]) ? (integer) $row[6] : null,	// Default value of the column (stored as is from the database, not type casted)
				'auto'		=> trim(strtolower($row[7])) === 'auto_increment' ? true : false,	// Whether or not the column auto-incrementing
			);

			// Set formatter :
			$n = count($columns) - 1;
			$columns[$n]['formatter'] = $this->get_formatter($columns[$n]['type']);
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

		// Create columns data structure :
		$pk = array();
		foreach($stmt as $row)
			$pk[] = $row[0];

		// Create and return new table object :
		return new \Glue\DB\Table($this->id, $name, $columns, $pk);
	}

	/**
	 * Loads table list by database introspection.
	 *
	 * @return array
	 */
	public function create_table_list() {
		$stmt = $this->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname");
		$stmt->execute(array(':dbname' => $this->dbname));
		$tables = array();
		while ($table = $stmt->fetchColumn())
			$tables[$table] = $table;
		return $tables;
	}

	/**
	 * Returns the appropriate formatter for given db type.
	 *
	 * @param string $dbtype
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function get_formatter($dbtype) {
		// Extract first word from type (MySQL may return things like "float unsigned" sometimes) :
		if (preg_match('/^\S+/', $dbtype, $matches))
			$dbtype = $matches[0];

		// Convert type to upper case :
		$dbtype = strtoupper($dbtype);

		// Create appropriate formatter :
		switch ($dbtype) {
			// Integer types :
			case 'TINYINT'; case 'SMALLINT'; case 'MEDIUMINT'; case 'INT'; case 'BIGINT';
				$formatter = new \Glue\DB\Formatter_Integer;
				break;

			// Real types :
			case 'FLOAT'; case 'DOUBLE'; case 'DECIMAL';
				$formatter = new \Glue\DB\Formatter_Float;
				break;

			// Boolean types :
			case 'BIT';
				$formatter = new \Glue\DB\Formatter_Boolean;
				break;

			// String types :
			case 'CHAR'; case 'VARCHAR'; case 'TINYTEXT'; case 'TEXT';
			case 'MEDIUMTEXT'; case 'LONGTEXT'; case 'ENUM'; case 'SET';
				$formatter = new \Glue\DB\Formatter_String;
				break;

			// Binary types :
			case 'BINARY'; case 'VARBINARY'; case 'TINYBLOB'; case 'BLOB';
			case 'MEDIUMBLOB'; case 'LONGBLOB';
				$formatter = new \Glue\DB\Formatter_String; // TODO Is this the right thing to do ?
				break;

			// Date and time types :
			case 'DATE'; case 'DATETIME'; case 'TIME'; case 'TIMESTAMP'; case 'YEAR';
				$formatter = new \Glue\DB\Formatter_String; // TODO Is this the right thing to do ?
				break;

			// Default :
			default;
				throw new \Glue\DB\Exception("Unknown MySQL data type : " . $dbtype);
		}

		return $formatter;
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
		return '`' . $identifier . '`';
	}
}