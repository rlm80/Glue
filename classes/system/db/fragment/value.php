<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a value in an SQL query.
 * 
 * The value can be of any PHP primitive types : integer, float, string, boolean, null or an array of
 * elements belonging to any of those primitive types.
 * 
 * The SQL output of a value fragment is the value properly quoted.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Value extends \Glue\DB\Fragment {
	/**
	 * @var mixed Value.
	 */
	protected $value;

	/**
	 * Constructor.
	 *
	 * @param mixed $value
	 */
	public function __construct($value = null) {
		$this->value($value);
	}

	/**
	 * Value setter/getter.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function value($value = null) {
		if (func_num_args() === 0)
			return $this->value;
		else {
			$this->value = $value;
			return $this;
		}
	}
}