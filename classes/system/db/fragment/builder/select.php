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
	 * Adds a list of columns to the select list, making sure they aren't included twice. The function accepts
	 * columns as a list of parameters or as an array.
	 *
	 * @return \Glue\DB\Fragment_Builder_Select
	 */
	public function columns() {
		// Get array of columns :
		$columns = func_get_args();
		if (is_array($columns[0]))
			$columns = $columns[0];
		
		// Add columns one by one :
		foreach($columns as $colid)
			$this->add_column(\Glue\DB\Fragment_Column::get($colid));
		
		// Return $this for chainability :
		return $this;
	}

	/**
	 * Adds a computed column to the select list. The function accepts :
	 * - a template, followed by replacements, followed by an alias,
	 * - any fragment followed by an alias.
	 *
	 * @return \Glue\DB\Fragment_Builder_Select
	 */
	public function computed() {
		// Split params :
		$params	= func_get_args();
		$first	= array_shift($params); 
		
		// Get alias :
		$alias = array_pop($params);
		
		// Build fragment :
		if ($first instanceof \Glue\DB\Fragment)
			$fragment = $first;
		else
			$fragment = new \Glue\DB\Fragment_Template($first, $params);
			
		// Add column :
		$this->push(new \Glue\DB\Fragment_Item_Select($fragment, $alias));
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
}