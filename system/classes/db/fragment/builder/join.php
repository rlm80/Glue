<?php

namespace Glue\DB;

/**
 * Fragment that represents a join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join extends Fragment_Builder {
	/**
	 * Initializes the expression with a first operand.
	 *
	 * @param mixed $operand Right operand of the join. It may be any fragment, or simply a table name.
	 * @param Fragment_Aliased_Table $alias Initialiazed with the actual fragment that will constitute the right operand of the join.
	 *
	 * @return Fragment_Aliased_Table
	 */
	public function init($operand, &$alias = null) { // TODO REMOVE init functions and ignore first operator if no fragment yet
		$this->reset();
		return $this->add($operand, null, $alias);
	}

	/**
	 * Adds an operand to the expression, using an inner join.
	 *
	 * @param mixed $operand Right operand of the join. It may be any fragment, or simply a table name.
	 * @param Fragment_Aliased_Table $alias Initialiazed with the actual fragment that will constitute the right operand of the join.
	 *
	 * @return Fragment_Aliased_Table
	 */
	public function inner($operand, &$alias = null) {
		return $this->add($operand, Fragment_Operand_Join::INNER_JOIN, $alias);
	}

	/**
	 * Adds an operand to the expression, using a left outer join.
	 *
	 * @param mixed $operand Right operand of the join. It may be any fragment, or simply a table name.
	 * @param Fragment_Aliased_Table $alias Initialiazed with the actual fragment that will constitute the right operand of the join.
	 *
	 * @return Fragment_Aliased_Table
	 */
	public function left($operand, &$alias = null) {
		return $this->add($operand, Fragment_Operand_Join::LEFT_OUTER_JOIN, $alias);
	}

	/**
	 * Adds an operand to the expression, using a right outer join.
	 *
	 * @param mixed $operand Right operand of the join. It may be any fragment, or simply a table name.
	 * @param Fragment_Aliased_Table $alias Initialiazed with the actual fragment that will constitute the right operand of the join.
	 *
	 * @return Fragment_Aliased_Table
	 */
	public function right($operand, &$alias = null) {
		return $this->add($operand, Fragment_Operand_Join::RIGHT_OUTER_JOIN, $alias);
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param Fragment $operand Table name, aliased table fragment or join fragment.
	 * @param integer $operator Operator.
	 * @param Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return Fragment_Aliased_Table
	 */
	protected function add($operand, $operator, &$alias) {
		// Operand is a table name ? Turn it into an aliased table fragment :
		if (is_string($operand))
			$operand = new Fragment_Aliased_Table($operand);

		// Assign operand to $alias parameter :
		if ($operand instanceof Fragment_Aliased_Table)
			$alias = $operand;
			
		// Build fragment :
		$fragment = new Fragment_Operand_Join($operand, $operator);
		
		// Give fragment context :
		$fragment->context($this);	

		// Add operand :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
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
		return $db->compile_builder_join($this, $style);
	}	
}