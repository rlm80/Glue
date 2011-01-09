<?php

namespace Glue\System\DB;

/**
 * Builders are fragments that provide a fluent API to build an assembly of children fragments.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class Fragment_Builder extends \Glue\DB\Fragment {
	/**
	 * @var array List of children fragments.
	 */
	protected $children = array();

	/**
	 * Adds a child at the end of the children list.
	 *
	 * @param \Glue\DB\Fragment $fragment
	 */
	protected function push(\Glue\DB\Fragment $fragment) {
		$this->children[] = $fragment;
	}	

	/**
	 * Removes the last child at the end of the children list.
	 *
	 * @return \Glue\DB\Fragment_Builder
	 */
	public function pop() {
		$fragment = array_pop($this->children);
		return $this;
	}

	/**
	 * Removes all children.
	 *
	 * @return \Glue\DB\Fragment_Builder
	 */
	public function reset() {
		$this->children = array();
		return $this;
	}
	
	/**
	 * Returns true if there is no children, false otherwise.
	 *
	 * @return boolean
	 */
	public function is_empty() {
		return empty($this->children);
	}	
	
	/**
	 * Returns first fragment pushed, or false if there is no such fragment.
	 *
	 * @return \Glue\DB\Fragment
	 */
	public function first() {
		return reset($this->children);
	}	
	
	/**
	 * Returns last fragment pushed, or false if there is no such fragment.
	 *
	 * @return \Glue\DB\Fragment
	 */
	public function last() {
		return end($this->children);
	}	

	/**
	 * Returns children fragments.
	 *
 	 * @return array
	 */
	public function &children() {
		return $this->children;
	}	
}