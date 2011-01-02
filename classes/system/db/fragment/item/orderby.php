<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an item in an order by expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Orderby extends \Glue\DB\Fragment_Item {
	/**
	 * @var \Glue\DB\Fragment Fragment to be ordered.
	 */
	protected $ordered;

	/**
	 * @var integer Order.
	 */
	protected $order;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $ordered
	 * @param integer $ordered
	 */
	public function __construct(\Glue\DB\Fragment $ordered, $order = null) {
		$this->ordered($ordered);
		$this->order($order);
	}
	
	/**
	 * Fragment getter/setter.
	 *
	 * @param \Glue\DB\Fragment $ordered
	 *
	 * @return mixed
	 */
	public function ordered(\Glue\DB\Fragment $ordered = null) {
		if (func_num_args() === 0)
			return $this->ordered;
		else {
			$this->ordered = $ordered;
			return $this;
		}
	}	

	/**
	 * Sets order to ASC.
	 *
	 * @return \Glue\DB\Fragment_Item_Orderby
	 */
	public function asc() {
		$this->order(\Glue\DB\DB::ASC);
		return $this;
	}

	/**
	 * Sets order to DESC.
	 *
	 * @return \Glue\DB\Fragment_Item_Orderby
	 */
	public function desc() {
		$this->order(\Glue\DB\DB::DESC);
		return $this;
	}

	/**
	 * Order getter/setter.
	 *
	 * @param integer $order
	 *
	 * @return mixed
	 */
	public function order($order = null) {
		if (func_num_args() === 0)
			return $this->order;
		else {
			$this->order = $order;
			return $this;
		}
	}
}