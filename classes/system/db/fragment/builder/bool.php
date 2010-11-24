<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool extends \Glue\DB\Fragment_Builder {
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
	 * Adds a boolean operand at the end of the expression, connecting it with
	 * the OR NOT operator.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function ornot() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\Fragment_Operand_Bool::ORNOT);
		return $this;
	}

	/**
	 * Adds a boolean operand at the end of the expression, connecting it with
	 * the AND NOT operator.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool
	 */
	public function andnot() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\Fragment_Operand_Bool::ANDNOT);
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
	 * Forwards call to given database.
	 *
	 * @param \Glue\DB\Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Database $db, $style) {
		// Forwards call to database :
		return $db->compile_builder_bool($this, $style);
	}
}