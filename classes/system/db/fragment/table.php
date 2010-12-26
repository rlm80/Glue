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
	 * @var string Table name.
	 */
	protected $table;

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 */
	public function __construct($table) {
		$this->table = $table;
	}

	/**
	 * Table setter/getter.
	 *
	 * @return mixed
	 */
	public function table($table = null) {
		if (func_num_args() === 0)
			return $this->table;
		else
			return $this->set_property('table', $table);
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