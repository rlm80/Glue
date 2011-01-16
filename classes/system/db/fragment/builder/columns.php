<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a list of columns.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Columns extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds columns at the end of the column list. Accepts :
	 * - a list of columns,
	 * - an array of columns.
	 *
	 * @return \Glue\DB\Fragment_Builder_Columns
	 */
	public function columns() {
		// Get array of columns to add :
		$args = func_get_args();
		if (is_array($args[0]))
			$columns = $args[0];
		else 
			$columns = $args; 
			
		// Add columns :
		foreach($columns as $column)
			$this->push(new \Glue\DB\Fragment_Item_Columns($column));
			
		return $this;
	}
}