<?php

namespace Glue\System\DB;

/**
 * Column class.
 *
 * TODO : describe this
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Column {
	/**
	 * @var \Glue\DB\Table Table this column belongs to.
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
	 * @var string Default value of the column (stored as is from the database, not type casted).
	 */
	protected $default;

	/**
	 * @var boolean Whether or not the column auto-incrementing.
	 */
	protected $auto;

	/**
	 * Constructor.
	 */
	public function __construct(\Glue\DB\Table $table, $name, $type, $nullable, $maxlength, $precision, $scale, $default, $auto) {
		$this->table		= $table;
		$this->name			= $name;
		$this->type			= $type;
		$this->nullable		= $nullable;
		$this->maxlength	= $maxlength;
		$this->precision	= $precision;
		$this->scale		= $scale;
		$this->default		= $default;
		$this->auto			= $auto;
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
	 * Returns the table of this column.
	 *
	 * @return \Glue\DB\Table
	 */
	public function table() {
		return $this->table;
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
	 * Returns the default value of the column (raw from the database, not type casted !).
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













