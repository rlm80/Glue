<?php

namespace Glue\System\DB;

/**
 * Float formatter class.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Formatter_Float extends \Glue\DB\Formatter {

	/**
	 * Formats data coming from the database into a format suitable for PHP.
	 *
	 * @param integer $data
	 */
	public function format($data) {
		if (isset($data))
			return (float) $data;
		else
			return null;
	}

	/**
	 * Formats data coming from PHP into a format suitable for insertino into the database.
	 *
	 * @param integer $data
	 */
	public function unformat($data) {
		return $data;
	}

	/**
	 * The PHP type returned by the format function.
	 *
	 * @return string
	 */
	public function type() {
		return 'float';
	}
}