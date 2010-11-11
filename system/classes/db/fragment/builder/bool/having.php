<?php

namespace Glue\DB;

/**
 * Fragment that provides a fluent interface to build a having clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool_Having extends Fragment_Builder_Bool {
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
		return $db->compile_builder_bool_having($this, $style);
	}	
}