<?php

namespace Glue\System\DB;

/**
 * Base fragement class for queries.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

abstract class Fragment_Query extends \Glue\DB\Fragment {
	/**
	 * Return current object. Example usage :
	 *
	 * $sql = DB::select('mytable')->where('1=1')->sql(); // Doesn't work ! Returns only the SQL of the last builder accessed : the where clause.
	 * $sql = DB::select('mytable')->where('1=1')->query()->sql(); // Works. Returns the SQL of the whole query.
	 *
	 * @return \Glue\DB\Fragment_Query
	 */
	public function query() {
		return $this;
	}
}