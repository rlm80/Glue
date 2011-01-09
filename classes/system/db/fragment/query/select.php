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
		$this->groupby	= new \Glue\DB\Fragment_Builder_Groupby();
		$this->having	= new \Glue\DB\Fragment_Builder_Bool();
		$this->orderby	= new \Glue\DB\Fragment_Builder_Orderby();
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
	 * With parameters, initialize the from clause and returns $this : @see \Glue\DB\Fragment_Builder_Join::init()
	 * Without parameters : returns from clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function from($table = null, &$obj = null) {
		if (func_num_args() > 0) {
			$this->from->init($table, $obj);
			return $this;
		}
		else
			return $this->from;
	}

	/**
	 * With parameters, initialize the from clause and returns $this : @see \Glue\DB\Fragment_Builder_Bool::init()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
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
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function andwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, 'and'), $args);
		return $this;
	}
	
	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function orwhere() {
		$args = func_get_args();
		call_user_func_array(array($this->where, 'or'), $args);
		return $this;
	}	

	/**
	 * With parameters, initialize the having clause and returns $this : @see \Glue\DB\Fragment_Builder_Bool::init()
	 * Without parameters : returns where clause.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function having() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->having, 'init'), $args);
			return $this;
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
		call_user_func_array(array($this->having, 'and'), $args);
		return $this;
	}
	
	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function orhaving() {
		$args = func_get_args();
		call_user_func_array(array($this->having, 'or'), $args);
		return $this;
	}		
	
	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::init() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function on() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->from, 'on'), $args);
			return $this;
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
		call_user_func_array(array($this->from, 'and'), $args);
		return $this;
	}
	
	/**
	 * @see \Glue\DB\Fragment_Builder_Bool::or() + return $this.
	 *
	 * @return \Glue\DB\Fragment_Query_Select
	 */
	public function oron() {
		$args = func_get_args();
		call_user_func_array(array($this->from, 'or'), $args);
		return $this;
	}	

	/**
	 * Limit getter / setter (+ return $this).
	 *
	 * @param integer $limit
	 *
	 * @return integer
	 */
	public function limit($limit = null) {
		if (func_num_args() === 0)
			return $this->limit;
		else
			return $this->limit = $limit;
	}

	/**
	 * Offset getter / setter (+ return $this).
	 *
	 * @param integer $offset
	 *
	 * @return integer
	 */
	public function offset($offset = null) {
		if (func_num_args() === 0)
			return $this->offset;
		else
			return $this->offset = $offset;
	}	
}