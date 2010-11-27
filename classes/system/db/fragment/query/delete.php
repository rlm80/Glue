<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a delete query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Delete extends \Glue\DB\Fragment_Query {
	/**
	 * @var \Glue\DB\Fragment_Aliased_Table Table to delete rows from.
	 */
	protected $from;

	/**
	 * @var \Glue\DB\Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * @var Integer Limit.
	 */
	protected $limit;

	/**
	 * @var Integer Offset.
	 */
	protected $offset;

	/**
	 * Constructor.
	 *
	 * @param string $table_name Name of the main table you're deleting from.
	 * @param \Glue\DB\Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 */
	public function __construct($table_name = null, &$alias = null) { // TODO think...why is this constructor different from the one of select query ?
		// Init children fragments :
		$this->where	= new \Glue\DB\Fragment_Builder_Bool_Where();
		$this->from		= new \Glue\DB\Fragment_Aliased_Table($table_name);

		// Set up dependecies :
		$this->where->register_user($this);
		$this->from->register_user($this);

		// Set up contexts :
		$this->where->context($this);
		$this->from->context($this);

		// Initialize alias parameter :
		$alias = $this->from;
	}

	/**
	 * From table getter/setter.
	 *
	 * @param mixed $table_name Table name.
	 *
	 * @return mixed
	 */
	public function from($table_name = null) {
		if (func_num_args() > 0) {
			$this->from->aliased(new \Glue\DB\Fragment_Table($table_name));
			return $this;
		}
		else
			return $this->from;
	}

	/**
	 * Returns the where clause, initializing it with given parameters if any.
	 *
	 * @return \Glue\DB\Fragment_Builder_Bool_Where
	 */
	public function where() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->where->reset();
			return call_user_func_array(array($this->where, 'init'), $args);
		}
		else
			return $this->where;
	}

	/**
	 * Limit getter/setter.
	 *
	 * @param integer $limit
	 *
	 * @return integer
	 */
	public function limit($limit = null) {
		if (func_num_args() === 0)
			return $this->limit;
		else
			return $this->set_property('limit', $limit);
	}

	/**
	 * Offset getter/setter.
	 *
	 * @param integer $offset
	 *
	 * @return integer
	 */
	public function offset($offset = null) {
		if (func_num_args() === 0)
			return $this->offset;
		else
			return $this->set_property('offset', $offset);
	}

	/**
	 * Returns database inferred from tables used in the query.
	 *
	 * @return \Glue\DB\Connection
	 */
	public function db() {
		return $this->from()->aliased()->table()->db();
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
		return $cn->compile_query_delete($this, $style);
	}
}