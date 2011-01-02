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
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function init($table, &$obj = null) {
		$this->reset();
		$this->add($table, null, $obj);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using an inner join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function inner($table, &$obj = null) {
		$this->add($table, \Glue\DB\DB::INNER, $obj);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using a left outer join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function left($table, &$obj = null) {
		$this->add($table, \Glue\DB\DB::LEFT, $obj);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using a right outer join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function right($table, &$obj = null) {
		$this->add($table, \Glue\DB\DB::RIGHT, $obj);
		return $this;
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param mixed $table $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param integer $operator Operator.
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 */
	protected function add($table, $operator, &$obj) {
		// Operand is a table name ? Turn it into an table fragment :
		$table = is_string($table) ? new \Glue\DB\Fragment_Table($table) : $table;

		// Assign operand to $obj parameter :
		$obj = $table;

		// Build fragment :
		$fragment = new \Glue\DB\Fragment_Item_Join($table, $operator);

		// Add operand :
		$this->push($fragment);
	}
	
	/**
	 * Initializes last on clause with given parameters and return $this.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function on() {
		$args = func_get_args();
		call_user_func_array(array($this->last()->on(), 'init'), $args);
		return $this;
	}

	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function _or() {
		$args = func_get_args();
		call_user_func_array(array($this->last()->on(), '_or'), $args);
		return $this;
	}

	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function _and() {
		$args = func_get_args();
		call_user_func_array(array($this->last()->on(), '_and'), $args);
		return $this;
	}
	
	/**
	 * Fowards to last on clause.
	 *
	 * @return \Glue\DB\Fragment_Builder_Join
	 */
	public function not() {
		$this->last()->on()->not();
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