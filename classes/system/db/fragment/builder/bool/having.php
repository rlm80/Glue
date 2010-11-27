<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a having clause.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Bool_Having extends \Glue\DB\Fragment_Builder_Bool {
	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Connection $cn, $style) {
		// Forwards call to connection :
		return $cn->compile_builder_bool_having($this, $style);
	}
}