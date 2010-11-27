<?php

namespace Glue\System\DB;

/**
 * Fragment that represents anything that compiles into "... ASC" or "... DESC".
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Ordered extends \Glue\DB\Fragment {
	// Order constants :
	const ASC	= 0;
	const DESC	= 1;

	/**
	 * @var \Glue\DB\Fragment Fragment that needs to have an order.
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
	 * Sets order to ASC.
	 *
	 * @return \Glue\DB\Fragment_Ordered
	 */
	public function asc() {
		return $this->order(\Glue\DB\Fragment_Ordered::ASC);
	}

	/**
	 * Sets order to DESC.
	 *
	 * @return \Glue\DB\Fragment_Ordered
	 */
	public function desc() {
		return $this->order(\Glue\DB\Fragment_Ordered::DESC);
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
	 * @param \Glue\DB\Fragment $ordered
	 *
	 * @return mixed
	 */
	public function ordered(\Glue\DB\Fragment $ordered = null) {
		if (func_num_args() === 0)
			return $this->ordered;
		else
			return $this->set_property('ordered', $ordered);
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
		return $cn->compile_ordered($this, $style);
	}
}