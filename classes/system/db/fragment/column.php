<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a column of a specific table - alias pair and compiles into
 * a "<table_alias>.<column_name>" SQL string. Each column object is assigned a unique
 * string identifier.
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
	 * @var integer Maximum column identifier attributed so far.
	 */
	static $maxid = 0;

	/**
	 * @var array Identifiers => column objects mapping.
	 */
	static $map = array();
	
	/**
	 * @var \Glue\DB\Fragment_Aliased_Table
	 */
	protected $table_alias;

	/**
	 * @var string Column name.
	 */
	protected $column;
	
	/**
	 * @var string Unique identifier.
	 */
	protected $id;	

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment_Aliased_Table $table_alias
	 * @param string $column
	 */
	protected function __construct(\Glue\DB\Fragment_Aliased_Table $table_alias, $column) {
		// Set properties :
		$this->set_property('table_alias', $table_alias);
		$this->set_property('column', $column);
		
		// Assign unique identifier :
		$this->id = '@' . static::$maxid ++ . '@';
		
		// Store in identifier => instances mapping array :
		static::$map[$id] = $this;
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
	
	/**
	 * Unique identifier getter.
	 *
	 * @return string
	 */
	public function id() {
		return $this->id;
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
	
	/**
	 * Get instances by id.
	 * 
	 * @param string $id
	 * 
	 * @return \Glue\DB\Fragment_Column 
	 */
	public static function get($id) {
		return static::$map[$id];
	}
}