<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an assignment in an update query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Assignment extends \Glue\DB\Fragment {
	/**
	 * @var \Glue\DB\Fragment_Column Left side of the assignment.
	 */
	protected $column;

	/**
	 * @var \Glue\DB\Fragment Right side of the assignment.
	 */
	protected $to;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment_Column $set
	 * @param mixed $to
	 */
	public function __construct(\Glue\DB\Fragment_Column $column, $to = null) {
		$this->column($column);
		$this->to($to);
	}

	/**
	 * Left side of the assignment getter/setter.
	 *
	 * @param \Glue\DB\Fragment_Column $column
	 *
	 * @return mixed
	 */
	public function column(\Glue\DB\Fragment_Column $column = null) {
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
			if ( ! $to instanceof \Glue\DB\Fragment)
				$to = new \Glue\DB\Fragment_Value($to);

			// Replace to by new fragment :
			return $this->set_property('to', $to);
		}
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
		return $cn->compile_assignment($this, $style);
	}
}