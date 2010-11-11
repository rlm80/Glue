<?php

namespace Glue\DB;

/**
 * Fragment that provides a fluent interface to build a list of columns.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Columns extends Fragment_Builder {
	/**
	 * Adds an column at the end of the columns list.
	 *
	 * @param Fragment_Column $column
	 *
	 * @return Fragment_Builder_Columns
	 */
	public function _and(Fragment_Column $column) {
		$this->push($column);
		return $this;
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
		return $db->compile_builder_columns($this, $style);
	}	
}