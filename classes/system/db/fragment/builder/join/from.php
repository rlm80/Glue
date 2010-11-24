<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a from clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join_From extends \Glue\DB\Fragment_Builder_Join {
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
		return $db->compile_builder_join_from($this, $style);
	}
}