<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a table with its alias in a FROM clause and compiles into "table AS alias".
 *
 * TODO describe access to column ids
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Table extends \Glue\DB\Fragment {
	/**
	 * @var array Maximum attributed ids for aliases, by table name.
	 */
	static protected $maxids = array();

	/**
	 * @var string Table.
	 */
	protected $table;

	/**
	 * @var string Alias.
	 */
	protected $alias;

	/**
	 * Constructor.
	 *
	 * @param string $table
	 * @param string $alias
	 */
	public function __construct($table, $alias = null) {
		// Autogenerate alias if none given :
		if (func_num_args() === 1)
			$alias = static::genalias($table);

		// Set properties :
		$this->table($table);
		$this->alias($alias);
	}

	/**
	 * Table getter/setter.
	 *
	 * @param string $table
	 *
	 * @return mixed
	 */
	public function table($table = null) {
		if (func_num_args() === 0)
			return $this->table;
		else {
			$this->table = $table;
			return $this;
		}
	}

	/**
	 * Alias getter/setter.
	 *
	 * @param string $alias
	 *
	 * @return mixed
	 */
	public function alias($alias = null) {
		if (func_num_args() === 0)
			return $this->alias;
		else {
			$this->alias = $alias;
			return $this;
		}
	}
	
	/**
	 * Returns identifier of given column quoted for inclusion in a template.
	 *
	 * @param string $column
	 *
	 * @return string
	 */	
	public function column($column) {
		return \Glue\DB\DB::quote_identifier(array(
			empty($this->alias) ? $this->table : $this->alias,
			$column
		));
	}

	/**
	 * Fowards to column().
	 */
	public function __get($column) {
		return $this->column($column);
	}
	
	/**
	 * Extract value of given column from PDOStatement row and type-casts it appropriately.
	 * 
	 * @param string $column
	 * @param array $row
	 * @param string $cnid
	 * 
	 * @return mixed
	 */
	public function value($column, array $row, $cnid = null) {
		// Get appropriate php type :
		$phptype = \Glue\DB\DB::cn($cnid)->table($this->table)->column($column)->phptype();
		
		// Extract value from row :
		$value = $row[$this->column($column)];
		
		// Cast value :
		settype($value, $phptype);
		
		return $value;
	}
	
	public function __call($name, $args) {
		if (count($args) === 2)
			return $this->value($name, $args[0], $args[1]);
		else
			return $this->value($name, $args[0]);
	}

	/**
	 * Generate unique alias for table $table.
	 *
	 * @param $table
	 *
	 * @return string
	 */
	static protected function genalias($table) {
		$id = isset(static::$maxids[$table]) ? static::$maxids[$table] + 1 : 0;
		$alias = $table . '_' . $id;
		static::$maxids[$table] = $id;
		return $alias;
	}
}