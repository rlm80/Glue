<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a join expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join extends \Glue\DB\Fragment_Builder {
	/**
	 * Initializes the expression with a first operand.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the join operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function init($table, &$obj = null) {
		$this->reset();
		return $this->add($table, null, $obj);
	}

	/**
	 * Adds an operand to the expression, using an inner join. When called on an empty expression, the operator is ignored.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the join operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function inner($table, &$obj = null) {
		return $this->add(
			$table, 
			$this->is_empty() ? null : \Glue\DB\DB::INNER, 
			$obj
		);
	}

	/**
	 * Adds an operand to the expression, using a left outer join. When called on an empty expression, the operator is ignored.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the join operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function left($table, &$obj = null) {
		return $this->add(
			$table, 
			$this->is_empty() ? null : \Glue\DB\DB::LEFT, 
			$obj
		);
	}

	/**
	 * Adds an operand to the expression, using a right outer join. When called on an empty expression, the operator is ignored.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the join operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function right($table, &$obj = null) {
		return $this->add(
			$table, 
			$this->is_empty() ? null : \Glue\DB\DB::RIGHT, 
			$obj
		);
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param mixed $table $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param integer $operator Operator.
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the join operand.
	 * 
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	protected function add($table, $operator, &$operand) {
		// Build operand :
		$operand = is_string($table) ? new \Glue\DB\Fragment_Table($table) : $table;

		// Add fragment :
		$this->push(new \Glue\DB\Fragment_Item_Join($operand, $operator));
		
		return $this;
	}
	
	/**
	 * Initializes last on clause with given parameters and return $this. If no parameters
	 * given, returns last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function on() {
		if (func_num_args() === 0)
			return $this->last()->on();
		else {
			$args = func_get_args();
			call_user_func_array(array($this->last()->on(), 'init'), $args);
			return $this;
		}
	}

	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function _or() {
		$args = func_get_args();
		call_user_func_array(array($this->on(), '_or'), $args);
		return $this;
	}

	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function _and() {
		$args = func_get_args();
		call_user_func_array(array($this->on(), '_and'), $args);
		return $this;
	}
	
	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function not() {
		$this->on()->not();
		return $this;
	}
	
	/*
	 * Sets up aliases for _or(), _and(). Required because
	 * keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
	}		
}