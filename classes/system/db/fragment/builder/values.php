<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a list of rows in an insert query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Values extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds rows of values to the list. Accepts :
	 * - a list of values,
	 * - a list of arrays of values,
	 * - an array of arrays of values.
	 * 
	 * @return \Glue\System\DB\Fragment_Builder_Values
	 */
	public function values() {
		// Get arguments :
		$args = func_get_args();
		
		// Add values :
		if (is_array($args[0])) {
			if ( ! is_array(reset($args[0]))) {
				// Deal with normal case : ->values(array(val1, val2, ...), array(val1, val2, ...), ...)
				foreach($args as $values_array)
					$this->push(new \Glue\DB\Fragment_Item_Values($values_array));
			}
			else {
				// Array of arrays given, call this function recursively for each of them :
				foreach($args as $arg)
					$this->values($arg);
			}
		}
		else
			// List of values given, call this function recursively with the same values as an array :
			$this->values($args);
	
		return $this;
	}
}