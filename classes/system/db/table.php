<?php

namespace Glue\System\DB;

/**
 * Table class.
 *
 * Holds introspected data about a database table.
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
	 * @var string Name of this table, as it is known to the application.
	 */
	protected $alias;

	/**
	 * @var string Name of this table, as it is known to the database.
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
	 * @param string $alias Table alias.
	 */
	public function __construct($cnid, $alias) {
		// Init basic properties :
		$this->cnid		= $cnid;
		$this->alias	= $alias;
		$this->name		= $this->init_name();

		// Get table info by introspection :
		$info = $this->cn()->table_info($this->name);

		// Build columns :
		$this->columns = array();
		foreach ($info['columns'] as $ic) {
			// Create column object :
			$column = new \Glue\DB\Column(
					$this,
					$ic['column'],
					$ic['type'],
					$ic['nullable'],
					$ic['maxlength'],
					$ic['precision'],
					$ic['scale'],
					$ic['default'],
					$ic['auto']
				);

			// Add columns :
			$this->columns[$column->alias()] = $column;
		}

		// Build pk :
		$this->pk = array();
		foreach($info['pk'] as $col) {
			foreach($this->columns as $column) {
				if ($column->name() === $col)
					break;
			}
			$this->pk[$column->alias()] = $column;
		}
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
	 * Returns the columns of this table as an array indexed by column names.
	 *
	 * @return array
	 */
	public function columns() {
		return $this->columns;
	}

	/**
	 * Returns whether or not given column name is part of this table.
	 *
	 * @param $name
	 *
	 * @return boolean
	 */
	public function column_exists($name) {
		return array_key_exists($name, $this->columns);
	}

	/**
	 * Returns an array with all available columns on this table, as an array of names indexed
	 * by names. Introduced for symetry with \Glue\DB\Connection::table_list() .
	 *
	 * @return array
	 */
	public function column_list() {
		$colnames = array_keys($this->columns());
		return array_combine($colnames, $colnames);
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

	/**
	 * Returns the alias under which a DB column will be known in PHP-land.
	 *
	 * @param \Glue\DB\Column $column
	 *
	 * @return string
	 */
	public function _get_column_alias(\Glue\DB\Column $column) {
		return $column->name();
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * @param \Glue\DB\Column $column
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function _get_column_formatter(\Glue\DB\Column $column) {
		return $this->cn()->get_formatter($column->type());
	}
}
