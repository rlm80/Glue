<?php

namespace Glue\DB;

/**
 * Fragment that represents an assignment in an update query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Assignment extends Fragment {
	/**
	 * @var Fragment_Column Left side of the assignment.
	 */
	protected $column;

	/**
	 * @var Fragment Right side of the assignment.
	 */
	protected $to;

	/**
	 * Constructor.
	 *
	 * @param Fragment_Column $set
	 * @param mixed $to
	 */
	public function __construct(Fragment_Column $column, $to = null) {
		$this->column($column);
		$this->to($to);
	}

	/**
	 * Left side of the assignment getter/setter.
	 *
	 * @param Fragment_Column $column
	 *
	 * @return mixed
	 */
	public function column(Fragment_Column $column = null) {
		if (func_num_args() === 0)
			return $this->column;
		else
			return $this->set_property('column', $column);
	}

	/**
	 * Right side of the assignment getter/setter.
	 *
	 * @param mixed $to
	 *
	 * @return mixed
	 */
	public function to($to = null) {
		if (func_num_args() === 0)
			return $this->to;
		else {
			// Turn parameter into a fragment if it isn't already :
			if ( ! $to instanceof Fragment)
				$to = new Fragment_Value($to);

			// Replace to by new fragment :
			return $this->set_property('to', $to);
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
		return $db->compile_assignment($this, $style);
	}	
}