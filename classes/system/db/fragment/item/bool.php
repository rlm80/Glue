<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an operand in a boolean expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Bool extends \Glue\DB\Fragment_Item {
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
		$this->operator($operator);
		$this->operand($operand);
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
		else {
			$this->operator = $operator;
			return $this;
		}
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
		else {
			$this->operand = $operand;
			return $this;
		}
	}
}