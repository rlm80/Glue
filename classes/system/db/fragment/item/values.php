<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a row of values in an insert query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Values extends \Glue\DB\Fragment_Item {
	/**
	 * @var array Array of values to be quoted, or fragments.
	 */
	protected $values;

	/**
	 * Constructor.
	 *
	 * @param array $values
	 */
	public function __construct(array $values) {
		$this->values($values);
	}

	/**
	 * Values setter/getter.
	 *
	 * @param array $values
	 *
	 * @return mixed
	 */
	public function values($values = null) {
		if (func_num_args() === 0)
			return $this->values;
		else {
			$this->values = $values;
			return $this;
		}
	}
}