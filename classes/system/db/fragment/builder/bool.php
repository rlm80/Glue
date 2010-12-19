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
	protected $not = false;
	
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
		$this->add($args, \Glue\DB\Fragment_Operand_Bool::_OR);
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
		$this->add($args, \Glue\DB\Fragment_Operand_Bool::_AND);
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

		// Build fragment :
		if ($first instanceof \Glue\DB\Fragment)
			$fragment = $first;
		else
			$fragment = new \Glue\DB\Fragment_Template($first, $values);
		$operand = new \Glue\DB\Fragment_Operand_Bool($fragment, $operator);

		// Give fragment a context :
		$operand->context($this);

		// Add operand :
		$this->push($operand);
	}
	
	/**
	 * Negate expression.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function not() {
		return $this->set_property('not', ! $this->not);
	}
	
	/**
	 * Returns whether or not expression is negated.
	 *
	 * @return boolean
	 */
	public function is_negated() {
		return $this->not;
	}	

	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Connection $cn, $style) {
		// Forwards call to connection :
		return $cn->compile_builder_bool($this, $style);
	}
}