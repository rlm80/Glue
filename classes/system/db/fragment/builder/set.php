<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build the set list in an update query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Set extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the set list.
	 *
	 * @param string $column Pseudo-sql describing the column, or a fragment.
	 * @param mixed $to A value to be assigned to the column, or a fragment.
	 *
	 * @return \Glue\DB\Fragment_Item_Set
	 */
	public function set($column, $to) {
		$this->push(new \Glue\DB\Fragment_Item_Set(
			$column	instanceof \Glue\DB\Fragment ? $column	: \Glue\DB\DB::tpl($column),
			$to		instanceof \Glue\DB\Fragment ? $to		: \Glue\DB\DB::val($to)
		));
		return $this;
	}
}