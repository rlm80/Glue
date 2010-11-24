<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a list of columns.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Columns extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an column at the end of the columns list.
	 *
	 * @param \Glue\DB\Fragment_Column $column
	 *
	 * @return \Glue\DB\Fragment_Builder_Columns
	 */
	public function _and(\Glue\DB\Fragment_Column $column) {
		$this->push($column);
		return $this;
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
		return $db->compile_builder_columns($this, $style);
	}
}