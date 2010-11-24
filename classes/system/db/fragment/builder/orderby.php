<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Orderby extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the order by. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return \Glue\DB\Fragment_Ordered
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Build fragment :
		if ($first instanceof \Glue\DB\Fragment)
			$fragment = new \Glue\DB\Fragment_Ordered($first);
		else
			$fragment = new \Glue\DB\Fragment_Ordered(new \Glue\DB\Fragment_Template($first, $params));

		// Give fragment a context :
		$fragment->context($this);

		// Add fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}

	/**
	 * Forwards call to given database.
	 *
	 * @param \Glue\DB\Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Database $db, $style) {
		// Forwards call to database :
		return $db->compile_builder_orderby($this, $style);
	}
}