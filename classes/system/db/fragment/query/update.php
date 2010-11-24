<?php

namespace Glue\System\DB;

use \Glue\DB\Fragment_Builder_Setlist,
	\Glue\DB\Fragment_Builder_Join_From,
	\Glue\DB\Fragment_Builder_Where,
	\Glue\DB\Fragment_Builder_Orderby,
	\Glue\DB\Fragment_Query;

/**
 * Update query data structure.
 *
 * @package GlueDB
 * @author R�gis Lemaigre
 * @license MIT
 */

class Fragment_Query_Update extends Fragment_Query {
	/**
	 * @var Fragment_Builder_Setlist Set list.
	 */
	protected $set;

	/**
	 * @var Fragment_Builder_Join_From From clause.
	 */
	protected $from;

	/**
	 * @var Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * @var Fragment_Builder_Orderby Order by list.
	 */
	protected $orderby;

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
	 */
	public function __construct() {
		// Init children fragments :
		$this->set		= new Fragment_Builder_Setlist();
		$this->from		= new Fragment_Builder_Join_From();
		$this->where	= new Fragment_Builder_Bool_Where();
		$this->orderby	= new Fragment_Builder_Orderby();

		// Set up dependecies :
		$this->set->register_user($this);
		$this->from->register_user($this);
		$this->where->register_user($this);
		$this->orderby->register_user($this);

		// Set up contexts :
		$this->set->context($this);
		$this->from->context($this);
		$this->where->context($this);
		$this->orderby->context($this);
	}

	/**
	 * Returns the set list, initializing it with given parameters if any.
	 *
	 * I.e. "$query->set(...)" is the same as "$query->set()->and(...)".
	 *
	 * @param Fragment_Column $column
	 * @param mixed $to
	 *
	 * @return Fragment_Builder_List_Set
	 */
	public function set($column = null, $to = null) {
		if (func_num_args() > 0) {
			$this->set->reset();
			return $this->set->and($column, $to);
		}
		return $this->set;
	}

	/**
	 * Returns the from clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->from(...)" is the same as "$query->from()->init(...)".
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return Fragment_Builder_Join
	 */
	public function from($operand = null, &$alias = null) {
		if (func_num_args() > 0) {
			$this->from->reset();
			return $this->from->init($operand, $alias);
		}
		return $this->from;
	}

	/**
	 * Returns the where clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->where(...)" is the same as "$query->where()->init(...)".
	 *
	 * @return Fragment_Builder_Bool_Where
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
	 * Returns the order by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->orderby(...)" is the same as "$query->orderby()->and(...)".
	 *
	 * @return Fragment_Builder_List_Orderby
	 */
	public function orderby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->orderby->reset();
			return call_user_func_array(array($this->orderby, 'and'), $args);
		}
		else
			return $this->orderby;
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
	 * @return Database
	 */
	public function db() {
		$op = $this->from();
		while ($op instanceof Fragment_Builder_Join)
			$op = $op->first()->operand();
		return $op->aliased()->table()->db();
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
		return $db->compile_query_update($this, $style);
	}
}