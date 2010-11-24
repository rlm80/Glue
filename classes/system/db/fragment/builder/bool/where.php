<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a where clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool_Where extends \Glue\DB\Fragment_Builder_Bool {
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
		return $db->compile_builder_bool_where($this, $style);
	}
}