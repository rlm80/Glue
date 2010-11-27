<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an operand in a join expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Operand_Join extends \Glue\DB\Fragment_Operand {
	// Join operators :
	const LEFT_OUTER_JOIN	= 0;
	const RIGHT_OUTER_JOIN	= 1;
	const INNER_JOIN		= 2;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool On clause.
	 */
	protected $on;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $operand
	 * @param integer $operator Null means first operand of join expression => no on clause.
	 */
	public function __construct(\Glue\DB\Fragment $operand, $operator = null) {
		parent::__construct($operand, $operator);
		$this->on = new \Glue\DB\Fragment_Builder_Bool();
		$this->on->register_user($this);
		$this->on->context($this);
	}

	/**
	 *  Returns the on clause, initializing it with given parameters if any.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function on() { // TODO think more about this function
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->on, 'init'), $args);
		}
		else
			return $this->on;
	}

	/**
	 * Forwards call to operand.
	 *
	 * @return \Glue\DB\Fragment_Operand_Join
	 */
	public function _as($alias) {
		$this->operand()->_as($alias);
		return $this;
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
		return $cn->compile_operand_join($this, $style);
	}
}