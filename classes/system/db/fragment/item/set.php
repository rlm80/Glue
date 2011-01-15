<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an assignment in an update query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Set extends \Glue\DB\Fragment_Item {
	/**
	 * @var string Column name.
	 */
	protected $set;

	/**
	 * @var \Glue\DB\Fragment Right side of the assignment.
	 */
	protected $to;

	/**
	 * Constructor.
	 *
	 * @param string $set
	 * @param \Glue\DB\Fragment $to
	 */
	public function __construct($set, \Glue\DB\Fragment $to) {
		$this->set($set);
		$this->to($to);
	}

	/**
	 * Left side of the assignment getter/setter.
	 *
	 * @param string $set
	 *
	 * @return \Glue\DB\Fragment_Item_Set
	 */
	public function set($set = null) {
		if (func_num_args() === 0)
			return $this->set;
		else {
			$this->set = $set;
			return $this;
		}
	}

	/**
	 * Right side of the assignment getter/setter.
	 *
	 * @param \Glue\DB\Fragment $to
	 *
	 * @return \Glue\DB\Fragment_Item_Set
	 */
	public function to(\Glue\DB\Fragment $to = null) {
		if (func_num_args() === 0)
			return $this->to;
		else {
			$this->to = $to;
			return $this;
		}
	}
}