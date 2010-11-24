<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build a group by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Groupby extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the group by. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return \Glue\DB\Fragment_Builder_Groupby
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Add fragment :
		if ($first instanceof \Glue\DB\Fragment)
			$this->push($first);
		else
			$this->push(new \Glue\DB\Fragment_Template($first, $params));

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
	protected function compile(\Glue\DB\Database $db, $style) {
		// Forwards call to database :
		return $db->compile_builder_groupby($this, $style);
	}
}