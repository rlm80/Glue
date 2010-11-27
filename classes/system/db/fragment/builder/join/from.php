<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a from clause.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Join_From extends \Glue\DB\Fragment_Builder_Join {
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
		return $cn->compile_builder_join_from($this, $style);
	}
}