<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build the set list in an update query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Setlist extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the set list
	 *
	 * @param \Glue\DB\Fragment_Column $column
	 * @param mixed $to
	 *
	 * @return \Glue\DB\Fragment_Assignment
	 */
	public function _and($column, $to = null) {
		// Build fragment :
		$fragment = new \Glue\DB\Fragment_Assignment($column, $to);

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
		return $db->compile_builder_setlist($this, $style);
	}
}