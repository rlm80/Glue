<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an item in an group by expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Groupby extends \Glue\DB\Fragment_Item {
	/**
	 * @var \Glue\DB\Fragment Fragment to be grouped.
	 */
	protected $grouped;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $grouped
	 */
	public function __construct(\Glue\DB\Fragment $grouped) {
		$this->grouped($grouped);
	}
	
	/**
	 * Fragment getter/setter.
	 *
	 * @param \Glue\DB\Fragment $grouped
	 *
	 * @return mixed
	 */
	public function grouped(\Glue\DB\Fragment $grouped = null) {
		if (func_num_args() === 0)
			return $this->grouped;
		else {
			$this->grouped = $grouped;
			return $this;
		}
	}
}