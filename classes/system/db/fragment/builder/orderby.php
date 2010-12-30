<?php

namespace Glue\System\DB;

/**
 * Fragment that provides a fluent interface to build an order by expression.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Builder_Orderby extends \Glue\DB\Fragment_Builder {
	/**
	 * Adds an element at the end of the list by ascending order. You may pass any fragment, or a column
	 * identifier, or a template with question marks as placeholders, followed by their replacements.
	 *
	 * @return \Glue\DB\Fragment_Builder_Orderby
	 */
	public function asc() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\DB::ASC);
		return $this;
	}
	
	/**
	 * Adds an element at the end of the list by ascending order. You may pass any fragment, or a column
	 * identifier, or a template with question marks as placeholders, followed by their replacements.
	 *
	 * @return \Glue\DB\Fragment_Builder_Orderby
	 */
	public function desc() {
		$args = func_get_args();
		$this->add($args, \Glue\DB\DB::DESC);
		return $this;
	}	
	
	/**
	 * Adds an element at the end of the list by order in last parameter. You may pass any fragment,
	 * or a column identifier, or a template with question marks as placeholders, followed by their replacements.
	 *
	 * @return \Glue\DB\Fragment_Builder_Orderby
	 */
	public function orderby() {
		$args = func_get_args();
		$order = array_pop($args);
		$this->add($args, $order);
		return $this;
	}		
	
	/**
	 * Adds an element at the end of the list.
	 *
	 * @return \Glue\DB\Fragment_Item_Orderby
	 */
	protected function add($args, $order = null) {
		// Split params :
		$first = array_shift($args);

		// Build fragment :
		if ($first instanceof \Glue\DB\Fragment)
			$fragment = new \Glue\DB\Fragment_Item_Orderby($first, $order);
		elseif (\Glue\DB\Fragment_Column::exists($first))
			$fragment = new \Glue\DB\Fragment_Item_Orderby(\Glue\DB\Fragment_Column::get($first), $order);
		else 
			$fragment = new \Glue\DB\Fragment_Item_Orderby(new \Glue\DB\Fragment_Template($first, $args), $order);

		// Give fragment a context :
		$fragment->context($this);

		// Add fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}
	
	/**
	 * Returns connector string to connect children fragments with in generated SQL.
	 *
 	 * @return string
	 */
	protected function connector() {
		return ', ';
	}
}