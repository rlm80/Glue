<?php

namespace Glue\System\DB;

/**
 * Base fragment class.
 *
 * A fragment is a data structure that describes a piece of SQL query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class Fragment {
	/**
	 * Compiles fragment according to given connection SQL dialect and return SQL.
	 * 
	 * @param string $cnid Connection id.
	 * 
	 * @return string
	 */
	public function sql($cnid = null) {
		return \Glue\DB\DB::cn($cnid)->compile($this);		
	}
}














