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
		$fragment->register_user($this);
		$this->invalidate();
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
	 * Returns first fragment pushed, or false if there is no such fragment.
	 *
	 * @return \Glue\DB\Fragment
	 */
	public function first() {
		return reset($this->children);
	}

	/**
	 * Removes the last child at the end of the children list.
	 *
	 * @return \Glue\DB\Fragment_Builder
	 */
	public function pop() {
		$fragment = array_pop($this->children);
		$fragment->unregister_user($this);
		$this->invalidate();
		return $this;
	}

	/**
	 * Removes all children.
	 *
	 * @return \Glue\DB\Fragment_Builder
	 */
	public function reset() {
		while (count($this->children) > 0)
			$this->pop();
		return $this;
	}

	/**
	 * Returns children fragments.
	 *
 	 * @return array
	 */
	public function children() {
		return $this->children;
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
		// Generate children fragment SQL strings :
		$sqls = array();
		foreach ($this->children() as $child)
			$sqls[] = $child->sql($cn, $style);

		// Return SQL :
		return implode($this->connector(), $sqls);
	}	
	
	/**
	 * Returns connector string to connect children fragments with in generated SQL.
	 *
 	 * @return string
	 */
	protected function connector() {
		return ' ';
	}	
}