<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build an order by expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Orderby extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds a list of columns to the order by list. TODO make so that it is possible to pass an array
	 * 
	 * A parameter can be :
	 * - a string (default order = asc),
	 * - a fragment (default order = asc),
	 * - an array(string, order),
	 * - an array(fragment, order).
	 *
	 * @return \Glue\DB\Fragment_Builder_Orderby
	 */
	public function orderby() { // TODO think...isn't it better to call this "add" or "and" ?
		// Get array of columns :
		$columns = func_get_args();
		
		// Add columns one by one :
		foreach($columns as $column) {
			// Split :
			if (is_array($column)) {
				$col	= is_string($column[0]) ? new \Glue\DB\Fragment_Template($column[0]) : $column[0];
				$order	= $column[1];
			}
			else {
				$col	= is_string($column) ? new \Glue\DB\Fragment_Template($column) : $column;
				$order	= \Glue\DB\DB::ASC; 
			}
						
			// Add column :
			$this->push(new \Glue\DB\Fragment_Item_Orderby($col, $order));
		}
		
		return $this;
	}
}