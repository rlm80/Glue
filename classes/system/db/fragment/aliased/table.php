<?php

namespace Glue\System\DB;

use \ArrayAccess;

/**
 * Fragment that represents a table - alias pair and compiles into a "<table> AS <alias>" SQL string.
 *
 * TODO describe access to column ids
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased_Table extends \Glue\DB\Fragment_Aliased {
	/**
	 * @var array Column fragments cache.
	 */
	protected $columns = array();

	/**
	 * Constructor.
	 *
	 * @param string $table
	 * @param string $alias
	 */
	public function __construct($table, $alias = null) {
		parent::__construct(new \Glue\DB\Fragment_Table($table), $alias);
	}

	/**
	 * Returns identifier of given child column fragment.
	 *
	 * @param string $column
	 *
	 * @return string
	 */
	public function __get($column) {
	    if ( ! isset($this->columns[$column]))
			$this->columns[$column] = new \Glue\DB\Fragment_Column($this, $column);
		return $this->columns[$column]->id();
	}
}