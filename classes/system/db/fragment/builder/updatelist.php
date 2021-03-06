<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build an update list of an update query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_UpdateList extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds elements at the end of the update list.
	 *
	 * @param mixed $arg1 A column name, or a names => values mapping array.
	 * @param mixed $arg2 A value to be assigned to the column (can also be a fragment).
	 * @return \Glue\DB\Fragment_Item_UpdateList
	 */
	public function set($arg1, $arg2 = null) {
		if (is_string($arg1)) {
			// Name, value pair given :
			$this->push(new \Glue\DB\Fragment_Item_UpdateList(
				$arg1,
				$arg2 instanceof \Glue\DB\Fragment ? $arg2 : \Glue\DB\DB::val($arg2)
			));
		}
		else {
			// Names => values mapping given :			
			foreach($arg1 as $col => $val)
				$this->set($col, $val);
		}
		return $this;
	}
}