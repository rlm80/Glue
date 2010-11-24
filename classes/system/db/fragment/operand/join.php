<?php

namespace Glue\System\DB;

use \Glue\DB\Fragment_Builder_Bool,
	\Glue\DB\Fragment_Operand;

/**
 * Fragment that represents an operand in a join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Operand_Join extends Fragment_Operand {
	// Join operators :
	const LEFT_OUTER_JOIN	= 0;
	const RIGHT_OUTER_JOIN	= 1;
	const INNER_JOIN		= 2;

	/**
	 * @var Fragment_Builder_Bool On clause.
	 */
	protected $on;

	/**
	 * Constructor.
	 *
	 * @param Fragment $operand
	 * @param integer $operator Null means first operand of join expression => no on clause.
	 */
	public function __construct(Fragment $operand, $operator = null) {
		parent::__construct($operand, $operator);
		$this->on = new Fragment_Builder_Bool();
		$this->on->register_user($this);
		$this->on->context($this);
	}

	/**
	 *  Returns the on clause, initializing it with given parameters if any.
	 *
	 * @return Fragment_Builder_Bool
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
	 * @return Fragment_Operand_Join
	 */
	public function _as($alias) {
		$this->operand()->_as($alias);
		return $this;
	}

	/**
	 * Forwards call to given database.
	 *
	 * @param Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(Database $db, $style) {
		// Forwards call to database :
		return $db->compile_operand_join($this, $style);
	}
}