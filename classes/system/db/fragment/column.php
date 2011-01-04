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
	const STYLE_UNQUALIFIED	= 1; // TODO virer ça

	/**
	 * @var integer Maximum column identifier attributed so far.
	 */
	static $maxid = 0;

	/**
	 * @var array Identifiers => column objects mapping.
	 */
	static $map = array();

	/**
	 * @var \Glue\DB\Fragment_Table
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
	public function __construct(\Glue\DB\Fragment_Table $table_alias, $column) {
		// Set properties :
		$this->table_alias	= $table_alias;
		$this->column		= $column;

		// Assign unique identifier :
		$this->id = '@' . static::$maxid ++ . '@';

		// Store in [identifier => instances] mapping array :
		static::$map[$this->id] = $this;
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
	 * Table fragment getter.
	 *
	 * @return \Glue\DB\Fragment_Table
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
	 * Returns true if a column with such an id exists, false otherwise.
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public static function exists($id) {
		return isset(static::$map[$id]);
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