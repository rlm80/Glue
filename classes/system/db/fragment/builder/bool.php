<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a boolean expression.
 * 
 * A boolean expression is a list of boolean operands. A boolean operand is a fragment paired with a boolean
 * operator (OR, AND). The whole expression can be negated.
 * 
 * The SQL output of a boolean expression is the SQL output of each of its operands put one after the other,
 * preceded with NOT and surrounded with parentheses if the expression is negated. The SQL output of a boolean
 * operand is its boolean operator followed by the SQL output of its fragment surrounded with parentheses.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool extends \Glue\DB\Fragment_Builder {
	/**
	 * @var boolean Whether or not this boolean expression should be negated.
	 */
	protected $negated = false;
	
	/**
	 * Resets the expression and initializes it with a first operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function init() {
		$this->reset();
		$args = func_get_args();
		return $this->add($args, null);
	}

	/**
	 * Use ->or() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the OR operator. When called on an empty expression, the operator is ignored.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function _or() {
		$args = func_get_args();
		return $this->add(
			$args,
			$this->is_empty() ? null : \Glue\DB\Fragment_Item_Bool::_OR
		);
	}

	/**
	 * Use ->and() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the AND operator. When called on an empty expression, the operator is ignored.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function _and() {
		$args = func_get_args();
		return $this->add(
			$args,
			$this->is_empty() ? null : \Glue\DB\Fragment_Item_Bool::_AND
		);
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param array $args
	 * @param integer $operator
	 */
	protected function add($args, $operator) {
		// Get template and replacement values :
		$values	= $args;
		$first	= array_shift($values);

		// Add new operand :
		$this->push(
			new \Glue\DB\Fragment_Item_Bool(
				is_string($first) ? new \Glue\DB\Fragment_Template($first, $values) : $first,
				$operator
			)
		);
		
		return $this;
	}
	
	/**
	 * Negate expression.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function not() {
		$this->negated = ! $this->negated;
		return $this;
	}
	
	/**
	 * Returns whether or not expression is negated.
	 *
	 * @return boolean
	 */
	public function negated() {
		return $this->negated;
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