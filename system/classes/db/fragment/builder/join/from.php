<?php

namespace Glue\DB;

/**
 * Fragment that provides a fluent interface to build a from clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join_From extends Fragment_Builder_Join {
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
		return $db->compile_builder_join_from($this, $style);
	}	
}