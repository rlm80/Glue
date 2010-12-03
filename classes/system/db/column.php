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
	 * @var \Glue\DB\Table Table object this column belongs to.
	 */
	protected $table;

	/**
	 * @var string Name of this column, as it is known to the application.
	 */
	protected $alias;

	/**
	 * @var string Name of this column, as it is known to the database.
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
	 * @var \Glue\DB\Formatter
	 */
	protected $formatter;

	/**
	 * Constructor.
	 */
	public function __construct(\Glue\DB\Table $table, $name, $type, $nullable, $maxlength, $precision, $scale, $default, $auto) {
		// Init basic properties :
		$this->table		= $table;
		$this->name			= $name;
		$this->type			= $type;
		$this->nullable		= $nullable;
		$this->maxlength	= $maxlength;
		$this->precision	= $precision;
		$this->scale		= $scale;
		$this->default		= $default;
		$this->auto			= $auto;

		// Get from table object :
		$this->formatter	= $this->table->_get_column_formatter($this);
		$this->alias		= $this->table->_get_column_alias($this);
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
	 * Returns the most appropriate PHP type.
	 *
	 * @return string
	 */
	public function phptype() {
		return $this->formatter->type();
	}

	/**
	 * Returns the formatter.
	 *
	 * @return \Glue\DB\Formatter
	 */
	public function formatter() {
		return $this->formatter;
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
	 * Returns the default value of the column (type casted).
	 *
	 * @param $typecast Whether or not to return typecasted data.
	 *
	 * @return string
	 */
	public function _default($typecast = true) {
		if ($typecast)
			return $this->formatter->format($this->default);
		else
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













