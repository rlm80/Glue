<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a table.
 *
 * @package    GlueDB
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
	 * Forwards call to given database.
	 *
	 * @param \Glue\DB\Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Database $db, $style) {
		// Forwards call to database :
		return $db->compile_table($this, $style);
	}
}