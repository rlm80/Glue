<?php

namespace Glue\DB;

/**
 * Fragment that represents a table - alias pair and compiles into a "<table> AS <alias>" SQL string.
 *
 * Also provides easy access to column fragments through the use of __get($column).
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased_Table extends Fragment_Aliased {
	/**
	 * @var boolean Prevents setting of table and alias once a column fragment has been generated.
	 */
	protected $lock = false; // TODO make sure this works...and think again about necessity of returning NEW column fragment

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 * @param string $alias
	 */
	public function __construct($table_name, $alias = null) {
		parent::__construct(new Fragment_Table($table_name), $alias);
	}

	/**
	 * Returns children column fragments.
	 *
	 * @param string $column
	 *
	 * @return Fragment_Column
	 */
	public function __get($column) {
		$this->lock = true;
	    return new Fragment_Column($this, $column);
	}
}