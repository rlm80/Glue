<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an aliased column in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased_Column extends \Glue\DB\Fragment_Aliased {
	/**
	 * Sets up binding between statement data and the column.
	 *
	 * @param \Glue\DB\Statement $stmt
	 * @param integer $index
	 * @param boolean $delayed
	 */
	public function bind(\Glue\DB\Statement $stmt, $index, $delayed) {
		$column	= $this->aliased();
		$alias	= $this->as();
		$column->bind($stmt, $alias, $index, $delayed);
	}
}