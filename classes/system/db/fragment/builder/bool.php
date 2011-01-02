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
	 * Initializes the expression with a first operand.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function init() {
		$this->reset();
		$args = func_get_args();
		$this->add($args, null);
		return $this;
	}

	/**
	 * Use ->or() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the OR operator.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function _or() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\Fragment_Item_Bool::_OR);
		return $this;
	}

	/**
	 * Use ->and() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the AND operator.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function _and() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\Fragment_Item_Bool::_AND);
		return $this;
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

		// Build fragment : TODO maybe try to shorten this a bit
		if ($first instanceof \Glue\DB\Fragment)
			$fragment = $first;
		else
			$fragment = new \Glue\DB\Fragment_Template($first, $values);
		$operand = new \Glue\DB\Fragment_Item_Bool($fragment, $operator);

		// Add operand :
		$this->push($operand);
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