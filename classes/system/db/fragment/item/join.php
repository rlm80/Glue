<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an operand in a join expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Join extends \Glue\DB\Fragment_Item {
	// Join operators :
	const LEFT_OUTER_JOIN	= 0;
	const RIGHT_OUTER_JOIN	= 1;
	const INNER_JOIN		= 2;
	
	/**
	 * @var integer Operator.
	 */
	protected $operator;

	/**
	 * @var \Glue\DB\Fragment Operand.
	 */
	protected $operand;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool On clause.
	 */
	protected $on;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $operand
	 * @param integer $operator Null means first operand of join expression.
	 */
	public function __construct(\Glue\DB\Fragment $operand, $operator = null) {
		$this->operator($operator);
		$this->operand($operand);		
		$this->on = new \Glue\DB\Fragment_Builder_Bool();
		$this->on->register_user($this);
		$this->on->context($this);
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
	 * Returns the on clause, initializing it with given parameters if any.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function on() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->on, 'init'), $args);
		}
		else
			return $this->on;
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
			switch ($this->operator) { // TODO add a function to Connection that returns the proper operator according to dialect
				case \Glue\DB\Fragment_Item_Join::INNER_JOIN :			$sql .= 'INNER JOIN ';			break;
				case \Glue\DB\Fragment_Item_Join::RIGHT_OUTER_JOIN :	$sql .= 'RIGHT OUTER JOIN ';	break;
				case \Glue\DB\Fragment_Item_Join::LEFT_OUTER_JOIN :		$sql .= 'LEFT OUTER JOIN ';		break;
			}
		}

		// Add operand SQL :
		$sqlop = $this->operand->sql($cn, $style);
		if ( ! $this->operand instanceof \Glue\DB\Fragment_Table)
			$sqlop	= '(' . $sqlop . ')';
		$sql .= $sqlop;

		// Add on SQL :
		if (isset($this->operator)) {
			$sqlon = $this->on->sql($cn, $style);
			$sql .= ' ON ' . $sqlon;
		}

		// Return SQL :
		return $sql;
	}
}