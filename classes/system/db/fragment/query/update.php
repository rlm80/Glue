<?php

namespace Glue\System\DB;

/**
 * Update query data structure.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Update extends \Glue\DB\Fragment_Query {
	/**
	 * @var \Glue\DB\Fragment Table.
	 */
	protected $table;

	/**
	 * @var \Glue\DB\Fragment_Builder_UpdateList Set list.
	 */
	protected $set;

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
		$this->set		= new \Glue\DB\Fragment_Builder_UpdateList();
		$this->where	= new \Glue\DB\Fragment_Builder_Bool();
		$this->orderby	= new \Glue\DB\Fragment_Builder_Orderby();
	}

	/**
	 * With parameters, initialize the table and returns $this.
	 * Without parameters : returns table.
	 *
	 * @param string $table
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public function table($table = null, &$operand = null) {
		if (func_num_args() > 0) {
			$operand = \Glue\DB\DB::table($table, null);
			$this->table = $operand;
			return $this;
		}
		else
			return $this->table;
	}

	/**
	 * With parameters, adds an element to the set list and returns $this : @see \Glue\DB\Fragment_Builder_UpdateList::set()
	 * Without parameters : returns the set list.
	 *
	 * @param mixed $arg1 A column name, or a names => values mapping array.
	 * @param mixed $arg2 A value to be assigned to the column (can also be a fragment).
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public function set($set = null, $to = null) {
		if (func_num_args() > 0) {
			$this->set->set($set, $to);
			return $this;
		}
		else
			return $this->set;
	}

	/**
	 * With parameters, adds item to the from clause, connecting it with AND and returns $this : @see \Glue\DB\Fragment_Builder_Bool::and()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public function where() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this, 'andwhere'), $args);
		}
		else
			return $this->where;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::and() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Update
	 */
	public function andwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, '_and'), $args);
		return $this;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Update
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
	 * @return \Glue\DB\Fragment_Query_Update
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
	 * @return \Glue\DB\Fragment_Query_Update
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
	 * @return \Glue\DB\Fragment_Query_Update
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