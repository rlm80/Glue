<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a select list.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Select extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds a list of columns to the select list, making sure they aren't included twice.
	 *
	 * @return \Glue\DB\Fragment_Builder_Select
	 */
	public function colin() {
		$params	= func_get_args();
		foreach($params as $param)
			$this->add_column(\Glue\DB\Fragment_Column::get($param));
		return $this;
	}

	/**
	 * Adds a column to the select list with the default alias, making sure it isn't included twice.
	 *
	 * @param \Glue\DB\Fragment_Column $column
	 */
	protected function add_column(\Glue\DB\Fragment_Column $column) {
		// Look for column in current list and return if found :
		foreach($this->children as $child) {
			if($child->selected() === $column && $child->alias() === $column->id())
				return;
		}

		// Add column :
		$this->push(new \Glue\DB\Fragment_Item_Select($column, $column->id()));
	}

	/**
	 * Removes a list of columns from the select list.
	 *
	 * @return \Glue\DB\Fragment_Builder_Select
	 */
	public function colout() {
		$params	= func_get_args();
		foreach($params as $param)
			$this->remove_column(\Glue\DB\Fragment_Column::get($param));
		return $this;
	}

	/**
	 * Adds a column to the select list with the default alias, making sure it isn't included twice.
	 *
	 * @param \Glue\DB\Fragment_Column $column
	 */
	protected function remove_column(\Glue\DB\Fragment_Column $column) {
		// Look for column in current list :
		$new = array();
		foreach($this->children as $child) {
			if($child->selected() === $column && $child->alias() === $column->id())
				return;
		}

		// Add column :
		$this->push(new \Glue\DB\Fragment_Item_Select($column, $column->id()));
	}

	/**
	 * Adds an element at the end of the select list. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return \Glue\DB\Fragment_Aliased
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Compute default alias :
		if ($first instanceof \Glue\DB\Fragment_Column)
			$alias = $this->compute_alias_column($first->column()->name());
		else
			$alias = $this->compute_alias_computed();

		// Build fragment :
		if ($first instanceof \Glue\DB\Fragment_Column)
			$fragment = new \Glue\DB\Fragment_Aliased_Column($first, $alias, count($this->children()));
		elseif ($first instanceof \Glue\DB\Fragment)
			$fragment = new \Glue\DB\Fragment_Aliased($first, $alias);
		else
			$fragment = new \Glue\DB\Fragment_Aliased(
				new \Glue\DB\Fragment_Template($first, $params),
				$alias
			);

		// Give fragment context :
		$fragment->context($this);

		// Push fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}

	/**
	 * Returns unique alias for computed column.
	 *
	 * @return string
	 */
	protected function compute_alias_computed() {
		// Count number of computed columns so far :
		$i = 0;
		foreach ($this->children as $child)
			if ( ! $child->aliased() instanceof \Glue\DB\Fragment_Column)
				$i++;

		// Compute alias :
		if ($i === 0)
			return 'computed';
		else
			return 'computed' . ($i + 1);
	}

	/**
	 * Returns unique alias for column.
	 *
	 * @return string
	 */
	protected function compute_alias_column($column_name) {
		// Count number of columns with such a name so far :
		$i = 0;
		foreach ($this->children as $child)
			if ($child->aliased() instanceof \Glue\DB\Fragment_Column)
				if ($child->aliased()->column()->name() === $column_name)
					$i++;

		// Compute alias :
		if ($i === 0)
			return $column_name;
		else
			return $column_name . ($i + 1);
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
		return $cn->compile_builder_selectlist($this, $style);
	}
}