<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a column of a specific table - alias pair and compiles into
 * a "<table_alias>.<column_name>" SQL string.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Column extends \Glue\DB\Fragment {
	/**
	 * @var integer Return SQL without table qualifier.
	 */
	const STYLE_UNQUALIFIED	= 1;

	/**
	 * @var \Glue\DB\Fragment_Aliased_Table
	 */
	protected $table_alias;

	/**
	 * @var \Glue\DB\Column
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment_Aliased_Table $table_alias
	 * @param string $column
	 */
	public function __construct(\Glue\DB\Fragment_Aliased_Table $table_alias, $column) {
		$this->table_alias	= $table_alias;
		$this->column		= $table_alias->aliased()->table()->column($column);
	}

	/**
	 * Column getter.
	 *
	 * @return \Glue\DB\Column
	 */
	public function column() {
		return $this->column;
	}

	/**
	 * Table alias getter.
	 *
	 * @return \Glue\DB\Fragment_Aliased_Table
	 */
	public function table_alias() {
		return $this->table_alias;
	}

	public function __toString() {
		return $this->sql($this->column()->table()->db());
	}

	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Connection $cn, $style) {
		// Forwards call to connection :
		return $cn->compile_column($this, $style);
	}
}