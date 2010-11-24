<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an operand in an expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class Fragment_Operand extends \Glue\DB\Fragment {
	/**
	 * @var integer Operator.
	 */
	protected $operator;

	/**
	 * @var \Glue\DB\Fragment Operand.
	 */
	protected $operand;

	/**
	 * Constructor.
	 * 
	 * @param \Glue\DB\Fragment $operand
	 * @param integer $operator Null means first operand.
	 */
	public function __construct(\Glue\DB\Fragment $operand, $operator = null) {
		$this->operand($operand);
		$this->operator($operator);
	}

	/**
	 * Operator getter/setter.
	 *
	 * @param integer $operator
	 *
	 * @return mixed
	 */
	public function operator($operator = null) {
		if (func_num_args() === 0)
			return $this->operator;
		else
			return $this->set_property('operator', $operator);
	}

	/**
	 * Operand getter/setter.
	 *
	 * @param \Glue\DB\Fragment $operand
	 *
	 * @return mixed
	 */
	public function operand(\Glue\DB\Fragment $operand = null) {
		if (func_num_args() === 0)
			return $this->operand;
		else
			return $this->set_property('operand', $operand);
	}
}