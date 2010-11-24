<?php

namespace Glue\System\DB;

use \Glue\DB\Fragment_Value,
	\Glue\DB\Fragment;

/**
 * Fragment that represents a row of values in an insert query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Row extends Fragment {
	/**
	 * @var array Value fragments.
	 */
	protected $values;

	/**
	 * Constructor.
	 *
	 * @param array $values
	 */
	public function __construct($values) {
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
			// Unregister old values :
			if (isset($this->values) && count($this->values) > 0)
				foreach($this->values as $value)
					$value->unregister_user($this);

			// Set new values :
			$this->values = array();
			foreach($values as $value) {
				// Turn values that aren't fragments into value fragments (SQL = quoted value) :
				if ( ! $value instanceof Fragment)
					$value = new Fragment_Value($value);

				// Set up dependency :
				$value->register_user($this);

				// Add value :
				$this->values[] = $value;
			}

			// Invalidate :
			$this->invalidate();

			return $this;
		}
	}

	/**
	 * Forwards call to given database.
	 *
	 * @param Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(Database $db, $style) {
		// Forwards call to database :
		return $db->compile_row($this, $style);
	}
}