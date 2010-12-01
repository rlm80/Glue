<?php

namespace Glue\System\DB;

/**
 * Table class.
 *
 * Holds introspected data about a cnid table.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Table {
	/**
	 * @var string Identifier of the connection that owns this table.
	 */
	protected $cnid;

	/**
	 * @var string Name of this table.
	 */
	protected $name;

	/**
	 * @var array Columns of this table.
	 */
	protected $columns;

	/**
	 * @var array Primary key columns of this table.
	 */
	protected $pk;

	/**
	 * Constructor.
	 *
	 * @param string $cnid Connection id.
	 * @param string $name Table name.
	 * @param string $columns Data structure representing columns.
	 * @param string $pk Data structure representing columns.
	 */
	public function __construct($cnid, $name, $columns, $pk) {
		$this->cnid		= $cnid;
		$this->name		= $name;

		// Build columns :
		$this->columns = array();
		foreach ($columns as $column) {
			$this->columns[$column['column']] = new \Glue\DB\Column(
					$this,
					$column['column'],
					$column['type'],
					$column['nullable'],
					$column['maxlength'],
					$column['precision'],
					$column['scale'],
					$column['default'],
					$column['auto']
				);
		}

		// Build pk :
		$this->pk = array();
		foreach($pk as $col)
			$this->pk[] = $this->columns[$col];
	}

	/**
	 * Returns the name of this table.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Returns the connection of this table.
	 *
	 * @return \Glue\DB\Connection
	 */
	public function cn() {
		return \Glue\DB\DB::cn($this->cnid);
	}

	/**
	 * Returns the primary key columns of this table.
	 *
	 * @return array
	 */
	public function pk() {
		return $this->pk;
	}

	/**
	 * Returns the columns of this table.
	 *
	 * @return array
	 */
	public function columns() {
		return $this->columns;
	}

	/**
	 * Returns a column.
	 *
	 * @param string $name
	 *
	 * @return \Glue\DB\Column
	 */
	public function column($name) {
		if ( ! isset($this->columns[$name]))
			throw new \Glue\DB\Exception("There is no column " . $name . " in table " . $this->name . " of connection " . $this->cnid . " .");
		return $this->columns[$name];
	}
}
