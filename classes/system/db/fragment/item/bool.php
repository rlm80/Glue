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
	// Boolean operators :
	const _AND		= 0;
	const _OR		= 1;
	
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

	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Connection $cn, $style) {
		// Initialize SQL with operator :
		$sql = '';
		if (isset($this->operator)) {
			switch ($this->operator) {
				case \Glue\DB\Fragment_Item_Bool::_AND :	$sql = 'AND ';		break;
				case \Glue\DB\Fragment_Item_Bool::_OR :		$sql = 'OR ';		break;
			}
		}

		// Operand :
		$sql .= '(' . $this->operand->sql($cn, $style) . ')';

		return $sql;
	}
}