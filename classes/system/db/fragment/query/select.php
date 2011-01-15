<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a select query.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Select extends \Glue\DB\Fragment_Query {
	/**
	 * @var \Glue\DB\Fragment_Builder_Select Select list.
	 */
	protected $select;

	/**
	 * @var \Glue\DB\Fragment_Builder_Join From clause.
	 */
	protected $from;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool Where clause.
	 */
	protected $where;

	/**
	 * @var \Glue\DB\Fragment_Builder_Groupby Group by list.
	 */
	protected $groupby;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool Having clause.
	 */
	protected $having;

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
		$this->select	= new \Glue\DB\Fragment_Builder_Select();
		$this->from		= new \Glue\DB\Fragment_Builder_Join();
		$this->where	= new \Glue\DB\Fragment_Builder_Bool();
		$this->orderby	= new \Glue\DB\Fragment_Builder_Orderby();
		$this->groupby	= new \Glue\DB\Fragment_Builder_Groupby();
		$this->having	= new \Glue\DB\Fragment_Builder_Bool();
	}

	/**
	 * With parameters, returns $this and add columns to the select list : @see \Glue\DB\Fragment_Builder_Select::columns()
	 * Without parameters : returns select list builder.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function columns() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->select, 'columns'), $args);
			return $this;
		}
		else
			return $this->select;
	}

	/**
	 * With parameters, initialize the from clause and returns $this : @see \Glue\DB\Fragment_Builder_Join::init()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function from($table = null, &$operand = null) {
		if (func_num_args() > 0) {
			$this->from->init($table, $operand);
			return $this;
		}
		else
			return $this->from;
	}

	/**
	 * Fowards call to from clause : @see \Glue\DB\Fragment_Builder_Join::left()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function left($table, &$operand = null) {
		$this->from->left($table, $operand);
		return $this;
	}

	/**
	 * Fowards call to from clause : @see \Glue\DB\Fragment_Builder_Join::right()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function right($table, &$operand = null) {
		$this->from->right($table, $operand);
		return $this;
	}

	/**
	 * Fowards call to from clause : @see \Glue\DB\Fragment_Builder_Join::inner()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function inner($table, &$operand = null) {
		$this->from->inner($table, $operand);
		return $this;
	}

	/**
	 * Fowards call to from clause : @see \Glue\DB\Fragment_Builder_Join::comma()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function comma($table, &$operand = null) {
		$this->from->comma($table, $operand);
		return $this;
	}

	/**
	 * With parameters, adds item to the last on clause, connecting it with AND and returns $this : @see \Glue\DB\Fragment_Builder_Bool::and()
	 * Without parameters : returns last on clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function on() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this, 'andon'), $args);
		}
		else
			return $this->from->on();
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::and() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function andon() {
		$args = func_get_args();
		call_user_func_array(array($this->from, '_and'), $args);
		return $this;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function oron() {
		$args = func_get_args();
		call_user_func_array(array($this->from, '_or'), $args);
		return $this;
	}

	/**
	 * With parameters, adds item to the from clause, connecting it with AND and returns $this : @see \Glue\DB\Fragment_Builder_Bool::and()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
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
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function andwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, '_and'), $args);
		return $this;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function orwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, '_or'), $args);
		return $this;
	}
	
	/**
	 * With parameters, adds item to the having clause, connecting it with AND and returns $this : @see \Glue\DB\Fragment_Builder_Bool::and()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function having() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this, 'andhaving'), $args);
		}
		else
			return $this->having;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::and() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function andhaving() {
		$args = func_get_args();
		call_user_func_array(array($this->having, '_and'), $args);
		return $this;
	}

	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function orhaving() {
		$args = func_get_args();
		call_user_func_array(array($this->having, '_or'), $args);
		return $this;
	}	

	/**
	 * With parameters, returns $this and add columns to the groupby list : @see \Glue\DB\Fragment_Builder_Groupby::groupby()
	 * Without parameters : returns groupby list builder.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function groupby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->groupby, 'groupby'), $args);
			return $this;
		}
		else
			return $this->groupby;
	}

	/**
	 * With parameters, returns $this and add columns to the orderby list : @see \Glue\DB\Fragment_Builder_Orderby::orderby()
	 * Without parameters : returns orderby list builder.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
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
	 * @return \Glue\DB\Fragment_Query_Select
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
	 * @return \Glue\DB\Fragment_Query_Select
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