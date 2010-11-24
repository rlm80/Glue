<?php

namespace Glue\System\DB;

use \Glue\DB\Fragment_Assignment,
	\Glue\DB\Fragment_Builder;

/**
 * Fragment that provides a fluent interface to build the set list in an update query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Setlist extends Fragment_Builder {
	/**
	 * Adds an element at the end of the set list
	 *
	 * @param Fragment_Column $column
	 * @param mixed $to
	 *
	 * @return Fragment_Assignment
	 */
	public function _and($column, $to = null) {
		// Build fragment :
		$fragment = new Fragment_Assignment($column, $to);

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
	 * @param Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(Database $db, $style) {
		// Forwards call to database :
		return $db->compile_builder_setlist($this, $style);
	}
}