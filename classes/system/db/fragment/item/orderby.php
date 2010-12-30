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
	 * @var \Glue\DB\Fragment Column to be ordered.
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
		else
			return $this->set_property('ordered', $ordered);
	}	

	/**
	 * Sets order to ASC.
	 *
	 * @return \Glue\DB\Fragment_Item_Orderby
	 */
	public function asc() {
		return $this->order(\Glue\DB\DB::ASC);
	}

	/**
	 * Sets order to DESC.
	 *
	 * @return \Glue\DB\Fragment_Item_Orderby
	 */
	public function desc() {
		return $this->order(\Glue\DB\DB::DESC);
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
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(\Glue\DB\Connection $cn, $style) {
		// Generate fragment SQL :
		$sql = $this->ordered->sql($cn, $style);
		if ( ! $this->ordered instanceof \Glue\DB\Fragment_Column)
			$sql = '(' . $sql . ')';

		// Add ordering :
		if (isset($this->order)) {
			switch ($this->order) {
				case \Glue\DB\DB::ASC :		$sql .= ' ASC';		break;
				case \Glue\DB\DB::DESC :	$sql .= ' DESC';	break;
				default : throw new \Exception("Unknown order constant : " . $this->order);
			}
		}

		// Return SQL :
		return $sql;
	}
}