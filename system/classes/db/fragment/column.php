<?php

namespace Glue\DB;

/**
 * Fragment that represents a column of a specific table - alias pair and compiles into
 * a "<table_alias>.<column_name>" SQL string.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Column extends Fragment {
	/**
	 * @var integer Return SQL without table qualifier.
	 */	
	const STYLE_UNQUALIFIED	= 1;
	
	/**
	 * @var Fragment_Aliased_Table
	 */
	protected $table_alias;

	/**
	 * @var Column
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param Fragment_Aliased_Table $table_alias
	 * @param string $column 
	 */
	public function __construct(Fragment_Aliased_Table $table_alias, $column) {
		$this->table_alias	= $table_alias;
		$this->column		= $table_alias->aliased()->table()->column($column);
	}

	/**
	 * Column getter.
	 *
	 * @return Column
	 */
	public function column() {
		return $this->column;
	}

	/**
	 * Table alias getter.
	 *
	 * @return Fragment_Aliased_Table
	 */
	public function table_alias() {
		return $this->table_alias;
	}

	public function __toString() {
		return $this->sql($this->column()->table()->db());
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
		return $db->compile_column($this, $style);
	}	
}