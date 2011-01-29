<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a group by list.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Groupby extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds a list of columns to the group by list.
	 * 
	 * A parameter can be :
	 * - a string,
	 * - a fragment.
	 *
	 * @return \Glue\DB\Fragment_Builder_Groupby
	 */
	public function groupby() {
		// Get array of columns :
		$columns = func_get_args();
		
		// Add columns one by one :
		foreach($columns as $column) {
			$this->push(new \Glue\DB\Fragment_Item_Groupby(
				is_string($column) ? new \Glue\DB\Fragment_SQL($column) : $column
			));
		}
		
		return $this;
	}
}