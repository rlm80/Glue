<?php

namespace Glue\System\DB;

use \ArrayAccess;

/**
 * Fragment that represents a table - alias pair and compiles into a "<table> AS <alias>" SQL string.
 *
 * Also provides easy access to column fragments through the use of $obj->__get($column), and access to the
 * result of a select query through the use of $obj['<column>'].
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased_Table extends \Glue\DB\Fragment_Aliased implements ArrayAccess {
	/**
	 * @var boolean Prevents setting of table and alias once a column fragment has been generated.
	 */
	protected $lock = false; // TODO make sure this works...

	/**
	 * @var array Column fragments cache.
	 */
	protected $columns = array();

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 * @param string $alias
	 */
	public function __construct($table_name, $alias = null) {
		parent::__construct(new \Glue\DB\Fragment_Table($table_name), $alias);
	}

	/**
	 * Returns children column fragments.
	 *
	 * @param string $column
	 *
	 * @return \Glue\DB\Fragment_Column
	 */
	public function __get($column) {
		$this->lock = true;
	    if ( ! isset($this->columns[$column]))
			$this->columns[$column] = new \Glue\DB\Fragment_Column($this, $column);
		return $this->columns[$column];
	}


	// ArrayAccess interface implementation :
	public function offsetExists ($offset) {
		return true;
	}

	public function offsetGet ($offset) {
		return $this->__get($offset)->value();
	}

	public function offsetSet ($offset, $value) {
		throw new Exception("Cannot set row values.");
	}

	public function offsetUnset ($offset) {
		throw new Exception("Cannot set row values.");
	}
}