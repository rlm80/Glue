<?php

namespace Glue\System\DB;

use \ArrayObject;

/**
 * Row class, as returned by the fetch method of \Glue\DB\Statement when using the FETCH_ARRAY fetching mode.
 *
 * Allows use of \Glue\DB\Fragment_Column indexes.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Row extends ArrayObject {
	public function offsetExists ($index) {
		if ($index instanceof \Glue\DB\Fragment_Column)
			$index = (string) $index;
		return parent::offsetExists($index);
	}

	public function offsetGet ($index) {
		if ($index instanceof \Glue\DB\Fragment_Column)
			$index = (string) $index;
		return parent::offsetGet($index);
	}

	public function offsetSet ($index, $newval) {
		if ($index instanceof \Glue\DB\Fragment_Column)
			$index = (string) $index;
		return parent::offsetSet($index, $newval);
	}

	public function offsetUnset ($index) {
		if ($index instanceof \Glue\DB\Fragment_Column)
			$index = (string) $index;
		return parent::offsetUnset($index);
	}
}