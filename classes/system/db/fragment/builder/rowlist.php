<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a list of rows in an insert query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Rowlist extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the rows list. You may pass an array of values,
	 * or an unlimited number of parameters.
	 *
	 * @return \Glue\DB\Fragment_Row
	 */
	public function _and() {
		// Get values :
		$args = func_get_args();
		if (is_array($args[0]))
			$values = $args[0];
		else
			$values = $args;

		// Build fragment :
		$fragment = new \Glue\DB\Fragment_Row($values);

		// Give fragment context :
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
		return $db->compile_builder_rowlist($this, $style);
	}
}