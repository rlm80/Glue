<?php

namespace Glue\System\DB;

/**
 * Fragment that holds a value that must be quoted.
 *
 * @package    GlueDB
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
	public function __construct($value) {
		$this->value = $value;
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
		else
			return $this->set_property('value', $value);
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
		return $db->compile_value($this, $style);
	}
}