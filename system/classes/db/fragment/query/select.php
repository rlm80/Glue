<?php

namespace Glue\DB;

use PDO;

/**
 * Fragment that represents a get query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class Fragment_Query_Select extends Fragment_Query {
	/**
	 * @var Fragment_Builder_SelectList Select list.
	 */
	protected $columns;

	/**
	 * @var Fragment_Builder_Join_From From clause.
	 */
	protected $from;

	/**
	 * @var Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * @var Fragment_Builder_Groupby Group by list.
	 */
	protected $groupby;

	/**
	 * @var Fragment_Builder_Bool_Having Having clause.
	 */
	protected $having;

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
		$this->columns	= new Fragment_Builder_SelectList();
		$this->from		= new Fragment_Builder_Join_From();
		$this->where	= new Fragment_Builder_Bool_Where();
		$this->groupby	= new Fragment_Builder_Groupby();
		$this->having	= new Fragment_Builder_Bool_Having();
		$this->orderby	= new Fragment_Builder_Orderby();

		// Set up dependecies :
		$this->columns->register_user($this);
		$this->from->register_user($this);
		$this->where->register_user($this);
		$this->groupby->register_user($this);
		$this->having->register_user($this);
		$this->orderby->register_user($this);
		
		// Set up contexts :
		$this->columns->context($this);
		$this->from->context($this);
		$this->where->context($this);
		$this->groupby->context($this);
		$this->having->context($this);
		$this->orderby->context($this);		
	}

	/**
	 * Returns the select list, initializing it with given parameters if any.
	 *
	 * I.e. "$query->columns(...)" is the same as "$query->columns()->and(...)".
	 *
	 * @return Fragment_Builder_SelectList
	 */
	public function columns() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->columns->reset();
			return call_user_func_array(array($this->columns, 'and'), $args);
		}
		else
			return $this->columns;
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
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->groupby(...)" is the same as "$query->groupby()->and(...)".
	 *
	 * @return Fragment_Builder_List_Groupby
	 */
	public function groupby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->groupby->reset();
			return call_user_func_array(array($this->groupby, 'and'), $args);
		}
		else
			return $this->groupby;
	}

	/**
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->having(...)" is the same as "$query->having()->init(...)".
	 *
	 * @return Fragment_Builder_Bool_Having
	 */
	public function having() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->having->reset();
			return call_user_func_array(array($this->having, 'init'), $args);
		}
		else
			return $this->having;
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
	 * Compiles this query into an SQL string and asks PDO to prepare it for execution. Returns
	 * a PDOStatement object that can be executed multiple times. If you need to execute a statement
	 * more than once, or if you need query parameters, this is the method of choice for security
	 * and performance.
	 *
	 * @return Statement
	 */
	public function prepare(array $driver_options = array()) {
		// Prepare statement :
		$stmt = parent::prepare($driver_options);
		$stmt->setFetchMode(PDO::FETCH_BOUND);
		
		// Bind columns :
		$this->bind($stmt, true);
		
		// Return statement :
		return $stmt;
	}

	/**
	 * Executes current query.
	 *
	 * @return Statement
	 */
	public function execute() {
		// Execute query and get statement :
		$db = $this->db();
		$sql = $this->sql($db);
		$stmt = $db->query($sql);
		$stmt->setFetchMode(PDO::FETCH_BOUND);
		
		// Bind columns :
		$this->bind($stmt, false);
		
		// Return statement :
		return $stmt;
	}
	
	/**
	 * Set up bindings between columns fetched by given statement and table alias objects
	 * of this query.
	 * 
	 * @param Statement $stmt
	 */
	protected function bind(Statement $stmt, $delayed) {
		$index = 0;
		foreach($this->columns()->children() as $child) {
			$index ++;
			if ($child instanceof Fragment_Aliased_Column)
				$child->bind($stmt, $index, $delayed);
		}
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
		return $db->compile_query_select($this, $style);
	}
}