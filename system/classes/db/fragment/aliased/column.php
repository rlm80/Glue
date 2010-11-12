<?php

namespace Glue\DB;

/**
 * Fragment that represents an aliased column in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased_Column extends Fragment_Aliased {
	/**
	 * Sets up binding between statement data and this column.
	 * 
	 * @param Statement $stmt
	 * @param boolean $delayed
	 */
	public function bind(Statement $stmt, $delayed) {
		$column	= $this->aliased();
		$alias	= $this->as();
		$column->bind($stmt, $alias, $delayed);
	}
	
	// ArrayAccess interface implementation :
	public function offsetExists ($offset)		{ return array_key_exists($this->row[$offset]); }
	public function offsetGet ($offset)			{ return $this->row[$offset]; }
	public function offsetSet ($offset, $value) { throw new Exception("Cannot set row values."); }
	public function offsetUnset ($offset)		{ throw new Exception("Cannot set row values."); }
}