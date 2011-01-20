<?php

namespace Glue\System\DB;

/**
 * Column class.
 *
 * Holds introspected data about a database column.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Column {
	/**
	 * @var string Id of the connection that owns the table of this column.
	 */
	protected $cnid;

	/**
	 * @var string Name of the table this column belongs to.
	 */
	protected $table;

	/**
	 * @var string Name of this column.
	 */
	protected $name;

	/**
	 * @var string Native database type.
	 */
	protected $type;

	/**
	 * @var string Most appropriate PHP type to represent the values of this column.
	 */
	protected $phptype;

	/**
	 * @var string Whether or not the column is nullable.
	 */
	protected $nullable;

	/**
	 * @var string Maximum length of the column (for text).
	 */
	protected $maxlength;

	/**
	 * @var string Precision of the column (total number of significant digits).
	 */
	protected $precision;

	/**
	 * @var string Scale of the column (number of significant digits in the decimal part).
	 */
	protected $scale;

	/**
	 * @var string Default value of the column.
	 */
	protected $default;

	/**
	 * @var boolean Whether or not the column is auto-incrementing.
	 */
	protected $auto;

	/**
	 * @var integer Ordinal position of column in create table statement.
	 */
	protected $position;

	/**
	 * Constructor.
	 */
	public function __construct($cnid, $table, $name, $type, $nullable, $maxlength, $precision, $scale, $default, $auto, $position, $phptype) {
		$this->cnid			= $cnid;
		$this->table		= $table;
		$this->name			= $name;
		$this->type			= $type;
		$this->nullable		= $nullable;
		$this->maxlength	= $maxlength;
		$this->precision	= $precision;
		$this->scale		= $scale;
		$this->default		= $default;
		$this->auto			= $auto;
		$this->position		= $position;
		$this->phptype		= $phptype;
	}

	/**
	 * Returns column name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Returns the connection that owns the table of this column.
	 *
	 * @return \Glue\DB\Connection
	 */
	public function cn() {
		return \Glue\DB\DB::cn($this->cnid);
	}

	/**
	 * Returns the table of this column.
	 *
	 * @return \Glue\DB\Table
	 */
	public function table() {
		return $this->cn()->table($this->table);
	}

	/**
	 * Returns the column database type.
	 *
	 * @return string
	 */
	public function type() {
		return $this->type;
	}

	/**
	 * Returns the most appropriate PHP type.
	 *
	 * @return string
	 */
	public function phptype() {
		return $this->phptype;
	}

	/**
	 * Returns whether or not the column can accept null values.
	 *
	 * @return boolean
	 */
	public function nullable() {
		return $this->nullable;
	}

	/**
	 * Returns the maximum length that column accepts.
	 *
	 * @return integer
	 */
	public function maxlength() {
		return $this->maxlength;
	}

	/**
	 * Returns the total number of significant digits of the column.
	 *
	 * @return integer
	 */
	public function precision() {
		return $this->precision;
	}

	/**
	 * Returns the number of significant digits in the decimal part af the column.
	 *
	 * @return integer
	 */
	public function scale() {
		return $this->scale;
	}

	/**
	 * Returns the default value of the column (as returned by the database).
	 *
	 * @return string
	 */
	public function _default() {
		return $this->default;
	}

	/**
	 * Whether or not the column is auto-incrementing.
	 *
	 * @return boolean
	 */
	public function auto() {
		return $this->auto;
	}

	public function __call($name, $args) {
		if ($name === 'default')
			return call_user_func_array(array($this, '_default'), $args);
	}
}













