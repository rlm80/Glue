<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a select query.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Where_Select extends \Glue\DB\Fragment_Query_Where {
	/**
	 * @var \Glue\DB\Fragment_Builder_Select Select list.
	 */
	protected $select;

	/**
	 * @var \Glue\DB\Fragment_Builder_Join From clause.
	 */
	protected $from;

	/**
	 * @var \Glue\DB\Fragment_Builder_Groupby Group by list.
	 */
	protected $groupby;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool Having clause.
	 */
	protected $having;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->select	= new \Glue\DB\Fragment_Builder_Select();
		$this->from		= new \Glue\DB\Fragment_Builder_Join();
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
}