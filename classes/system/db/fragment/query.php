<?php

namespace Glue\System\DB;

/**
 * Base fragement class for queries.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

abstract class Fragment_Query extends \Glue\DB\Fragment {
	/**
	 * @var integer Number of rows affected by the last call to execute().
	 */
	protected $row_count;

	/**
	 * Number of rows affected by the last call to execute().
	 *
	 * @return integer
	 */
	public function rowCount() {
		return $this->row_count;
	}

	/**
	 * Returns database object, determined from the tables this query manipulates.
	 *
	 *  @return \Glue\DB\Connection
	 */
	abstract public function db();

	/**
	 * Return current object. Example usage :
	 *
	 * $sql = DB::select('mytable')->where('1=1')->sql(); // Doesn't work ! Returns only the SQL of the last builder accessed : the where clause.
	 * $sql = DB::select('mytable')->where('1=1')->query()->sql(); // Works. Returns the SQL of the whole query.
	 *
	 * @return \Glue\DB\Fragment_Query
	 */
	public function query() {
		return $this;
	}

	/**
	 * Compiles this query into an SQL string and asks PDO to prepare it for execution. Returns
	 * a PDOStatement object that can be executed multiple times. If you need to execute a statement
	 * more than once, or if you need query parameters, this is the method of choice for security
	 * and performance.
	 *
	 * @return \Glue\DB\Statement
	 */
	public function prepare(array $driver_options = array()) {
		$cn = $this->db();
		$sql = $this->sql($cn);
		return $cn->prepare($sql, $driver_options);
	}

	/**
	 * Executes current query.
	 *
	 * @return \Glue\DB\Fragment_Query
	 */
	public function execute() {
		$cn = $this->db();
		$sql = $this->sql($cn);
		$this->row_count = (integer) $cn->exec($sql);
		return $this;
	}
}