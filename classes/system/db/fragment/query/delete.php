<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a delete query.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Delete extends \Glue\DB\Fragment_Query {
	/**
	 * @var \Glue\DB\Fragment Table.
	 */
	protected $table;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool Where clause.
	 */
	protected $where;

	/**
	 * @var \Glue\DB\Fragment_Builder_Orderby Order by list.
	 */
	protected $orderby;

	/**
	 * @var Integer Limit.
	 */
	protected $limit;

	/**
	 * @var Integer Offset.
	 */
	protected $offset;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->where	= new \Glue\DB\Fragment_Builder_Bool();
		$this->orderby	= new \Glue\DB\Fragment_Builder_Orderby();
	}

	/**
	 * With parameters, initialize the table and returns $this.
	 * Without parameters : returns table.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function table($table = null, &$operand = null) {
		if (func_num_args() > 0) {
			// Build table fragment :
			if (is_string($table))
				$operand = \Glue\DB\DB::table($table, null);
			elseif (is_array($table))
				$operand = \Glue\DB\DB::table($table[0], $table[1]);
			else
				$operand = $table;

			// Assign table fragment :
			$this->table = $operand;

			return $this;
		}
		else
			return $this->table;
	}

	/**
	 * With parameters, initialize the from clause and returns $this : @see \Glue\DB\Fragment_Builder_Bool::init()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function where() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->where, 'init'), $args);
			return $this;
		}
		else
			return $this->where;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::and() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function andwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, '_and'), $args);
		return $this;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function orwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, '_or'), $args);
		return $this;
	}

	/**
	 * With parameters, returns $this and add columns to the orderby list : @see \Glue\DB\Fragment_Builder_Orderby::orderby()
	 * Without parameters : returns orderby list builder.
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function orderby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->orderby, 'orderby'), $args);
			return $this;
		}
		else
			return $this->orderby;
	}

	/**
	 * Limit getter / setter (+ return $this).
	 *
	 * @param integer $limit
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function limit($limit = null) {
		if (func_num_args() === 0)
			return $this->limit;
		else {
			$this->limit = $limit;
			return $this;
		}
	}

	/**
	 * Offset getter / setter (+ return $this).
	 *
	 * @param integer $offset
	 *
	 * @return \Glue\DB\Fragment_Query_Delete
	 */
	public function offset($offset = null) {
		if (func_num_args() === 0)
			return $this->offset;
		else {
			$this->offset = $offset;
			return $this;
		}
	}
}