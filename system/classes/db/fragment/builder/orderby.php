<?php

namespace Glue\DB;

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Orderby extends Fragment_Builder {
	/**
	 * Adds an element at the end of the order by. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return Fragment_Ordered
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Build fragment :
		if ($first instanceof Fragment)
			$fragment = new Fragment_Ordered($first);
		else
			$fragment = new Fragment_Ordered(new Fragment_Template($first, $params));

		// Add fragment :
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
		return $db->compile_builder_orderby($this, $style);
	}	
}