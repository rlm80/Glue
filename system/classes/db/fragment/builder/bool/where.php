<?php

namespace Glue\DB;

/**
 * Fragment that provides a fluent interface to build a where clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool_Where extends Fragment_Builder_Bool {
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
		return $db->compile_builder_bool_where($this, $style);
	}	
}