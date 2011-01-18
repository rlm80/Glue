<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an insert query.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Insert extends \Glue\DB\Fragment_Query {
	/**
	 * @var \Glue\DB\Fragment Table.
	 */
	protected $table;

	/**
	 * @var \Glue\DB\Fragment_Builder_Values Values list.
	 */
	protected $values;

	/**
	 * @var \Glue\DB\Fragment_Builder_Columns Columns list.
	 */
	protected $columns;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->values	= new \Glue\DB\Fragment_Builder_Values();
		$this->columns	= new \Glue\DB\Fragment_Builder_Columns();
	}

	/**
	 * With parameters, initialize the table and returns $this.
	 * Without parameters : returns table.
	 *
	 * @param string $table
	 * @return \Glue\DB\Fragment_Query_Insert
	 */
	public function table($table = null) {
		if (func_num_args() > 0) {
			$this->table = \Glue\DB\DB::table($table, null);
			return $this;
		}
		else
			return $this->table;
	}

	/**
	 * With parameters, adds rows of values.
	 * Without parameters, returns rows of values list.
	 *
	 * @return \Glue\DB\Fragment_Query_Insert
	 */
	public function values() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->values, 'values'), $args);
			return $this;
		}
		else
			return $this->values;
	}

	/**
	 * With parameters, adds columns to the column list.
	 * Without parameters, returns column list.
	 *
	 * @return \Glue\DB\Fragment_Query_Insert
	 */
	public function columns() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->columns, 'columns'), $args);
			return $this;
		}
		else
			return $this->columns;
	}
}