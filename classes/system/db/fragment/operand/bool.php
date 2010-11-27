<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an operand in a boolean expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Operand_Bool extends \Glue\DB\Fragment_Operand {
	// Boolean operators :
	const _AND		= 0;
	const _OR		= 1;
	const ANDNOT	= 2;
	const ORNOT		= 3;

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
		return $cn->compile_operand_bool($this, $style);
	}
}