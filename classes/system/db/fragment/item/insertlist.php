<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a column in the list of columns of an insert query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_InsertList extends \Glue\DB\Fragment_Item {
	/**
	 * @var string Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param string $column Column
	 */
	public function __construct($column) {
		$this->column($column);
	}

	/**
	 * Column setter/getter.
	 *
	 * @param string $column
	 *
	 * @return mixed
	 */
	public function column($column = null) {
		if (func_num_args() === 0)
			return $this->column;
		else {
			$this->column = $column;
			return $this;
		}
	}
}