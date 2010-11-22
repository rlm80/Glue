<?php

namespace Glue\DB;

/**
 * Fragment that represents anything that compiles into "... ASC" or "... DESC".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Ordered extends Fragment {
	// Order constants :
	const ASC	= 0;
	const DESC	= 1;

	/**
	 * @var Fragment Fragment that needs to have an order.
	 */
	protected $ordered;

	/**
	 * @var integer Order.
	 */
	protected $order;

	/**
	 * Constructor.
	 *
	 * @param Fragment $ordered
	 * @param integer $ordered
	 */
	public function __construct(Fragment $ordered, $order = null) {
		$this->ordered($ordered);
		$this->order($order);
	}

	/**
	 * Sets order to ASC.
	 *
	 * @return Fragment_Ordered
	 */
	public function asc() {
		return $this->order(Fragment_Ordered::ASC);
	}

	/**
	 * Sets order to DESC.
	 *
	 * @return Fragment_Ordered
	 */
	public function desc() {
		return $this->order(Fragment_Ordered::DESC);
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
		else
			return $this->set_property('order', $order);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param Fragment $ordered
	 *
	 * @return mixed
	 */
	public function ordered(Fragment $ordered = null) {
		if (func_num_args() === 0)
			return $this->ordered;
		else
			return $this->set_property('ordered', $ordered);
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
		return $db->compile_ordered($this, $style);
	}	
}