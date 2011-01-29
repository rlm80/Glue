<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a select list.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_SelectList extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds a list of columns to the select list, making sure the same alias isn't used twice.
	 * 
	 * A parameter can be :
	 * - a string,
	 * - a fragment,
	 * - an array(string, alias),
	 * - an array(fragment, alias).
	 *
	 * @return \Glue\DB\Fragment_Builder_SelectList
	 */
	public function columns() {
		// Get array of columns :
		$columns = func_get_args();
		
		// Add columns one by one :
		foreach($columns as $column) {
			// Split :
			if (is_array($column)) {
				$col	= is_string($column[0]) ? new \Glue\DB\Fragment_SQL($column[0]) : $column[0];
				$alias	= $column[1];
			}
			else {
				$col	= is_string($column) ? new \Glue\DB\Fragment_SQL($column) : $column;
				$alias	= is_string($column) ? $column : null; 
			}
			
			// Skip column if already in the select list :
			$found = false;
			foreach($this->children() as $child) {
				if($child->alias() === $alias) {
					$found = true;
					break;
				}
			}
			if ($found) continue;
						
			// Add column :
			$this->push(new \Glue\DB\Fragment_Item_SelectList($col, $alias));
		}
		
		return $this;
	}
}