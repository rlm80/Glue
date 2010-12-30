<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a join expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join extends \Glue\DB\Fragment_Builder {
	/**
	 * Initializes the expression with a first operand.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	public function init($table, &$obj = null) {
		$this->reset();
		return $this->add($table, null, $obj);
	}

	/**
	 * Adds an operand to the expression, using an inner join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	public function inner($table, &$obj = null) {
		return $this->add($table, \Glue\DB\Fragment_Item_Join::INNER_JOIN, $obj);
	}

	/**
	 * Adds an operand to the expression, using a left outer join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	public function left($table, &$obj = null) {
		return $this->add($table, \Glue\DB\Fragment_Item_Join::LEFT_OUTER_JOIN, $obj);
	}

	/**
	 * Adds an operand to the expression, using a right outer join.
	 *
	 * @param mixed $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	public function right($table, &$obj = null) {
		return $this->add($table, \Glue\DB\Fragment_Item_Join::RIGHT_OUTER_JOIN, $obj);
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param mixed $table $table Right operand of the join. It may be a table name, a table-alias object or any fragment (most likely a join fragment for nested joins).
	 * @param integer $operator Operator.
	 * @param \Glue\DB\Fragment_Table $obj Initialiazed with the actual table-alias object.
	 *
	 * @return \Glue\DB\Fragment_Item_Join
	 */
	protected function add($table, $operator, &$obj) {
		// Operand is a table name ? Turn it into an table fragment :
		$table = is_string($table) ? new \Glue\DB\Fragment_Table($table) : $table;

		// Assign operand to $obj parameter :
		$obj = $table;

		// Build fragment :
		$fragment = new \Glue\DB\Fragment_Item_Join($table, $operator);

		// Give fragment context :
		$fragment->context($this);

		// Add operand :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}
}