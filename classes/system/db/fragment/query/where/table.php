<?php

namespace Glue\System\DB;

/**
 * Fragment that represent a query with a where clause, but dealing with a single table (no joins).
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Where_Table extends \Glue\DB\Fragment_Query_Where {
	/**
	 * @var \Glue\DB\Fragment Table.
	 */
	protected $table;

	/**
	 * With parameters, initialize the table and returns $this
	 * Without parameters : returns table.
	 *
	 * @return \Glue\DB\Fragment_Query_Where_Table
	 */
	public function table($table = null, &$operand = null) {
		if (func_num_args() > 0) {
			// Build table fragment :
			if (is_string($table))
				$this->table = db::table($table, null);
			elseif (is_array($table))
				$this->table = new \Glue\DB\Fragment_Table($table[0], $table[1]);
			else
				$this->table = $table;
			return $this;
		}
		else
			return $this->table;
	}
}