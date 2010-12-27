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

class Fragment_Alias extends \Glue\DB\Fragment {
	/**
	 * @var array Column fragments cache.
	 */
	protected $columns = array();
		
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
		else
			return $this->set_property('table', $table);
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
		else
			return $this->set_property('alias', $alias);
	}
	
	/**
	 * Returns identifier of given column fragment.
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

	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	function compile(\Glue\DB\Connection $cn, $style) {
		// Forwards call to connection :
		return $cn->compile_alias($this, $style);
	}
}