<?php

namespace Glue\System\DB;

use \Glue\DB\Fragment;

/**
 * Fragment that represents an operand in an expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class Fragment_Operand extends Fragment {
	/**
	 * @var integer Operator.
	 */
	protected $operator;

	/**
	 * @var Fragment Operand.
	 */
	protected $operand;

	/**
	 * Constructor.
	 *
	 * @param integer $operator Null means first operand.
	 */
	public function __construct(Fragment $operand, $operator = null) {
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
	 * @param Fragment $operand
	 *
	 * @return mixed
	 */
	public function operand(Fragment $operand = null) {
		if (func_num_args() === 0)
			return $this->operand;
		else
			return $this->set_property('operand', $operand);
	}
}