<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a table.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Table extends \Glue\DB\Fragment {
	/**
	 * @var \Glue\DB\Table Table.
	 */
	protected $table;

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 */
	public function __construct($table_name) {
		$this->table = \Glue\DB\DB::table($table_name);
	}

	/**
	 * Table setter/getter.
	 *
	 * @return mixed
	 */
	public function table($table_name = null) {
		if (func_num_args() === 0)
			return $this->table;
		else {
			$table = \Glue\DB\DB::table($table_name);
			return $this->set_property('table', $table);
		}
	}

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
		return $cn->compile_table($this, $style);
	}
}