<?php

namespace Glue\DB;

/**
 * Fragment that represents an operand in a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Operand_Bool extends Fragment_Operand {
	// Boolean operators :
	const _AND		= 0;
	const _OR		= 1;
	const ANDNOT	= 2;
	const ORNOT		= 3;
	
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
		return $db->compile_operand_bool($this, $style);
	}		
}