<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a column of a specific table - alias pair and compiles into
 * a "<table_alias>.<column_name>" SQL string.
 *
 * @package    GlueDB
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
	 * @var array Value of column in current row of data.
	 */
	protected $value;

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
	 * Value getter.
	 *
	 * @return mixed
	 */
	public function value() {
		return $this->value;
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
	 * Binds $this->value to column $alias at index $index of statement $stmt.
	 *
	 * @param \Glue\DB\Statement $stmt
	 * @param string $alias
	 * @param integer $index
	 * @param boolean $delayed
	 */
	public function bind(\Glue\DB\Statement $stmt, $alias, $index, $delayed) {
		// Bind column :
		if ($delayed)
			$stmt->bindColumnDelayed($index, $this->value);
		else
			$stmt->bindColumn($index, $this->value);

		// Bind formatters :
		$formatter = $this->column()->formatter();
		$stmt->bindFormatter($alias, $formatter);
		$stmt->bindFormatter($index, $formatter);

		return $this;
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
		return $db->compile_column($this, $style);
	}
}