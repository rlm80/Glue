<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an item in a select list.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Item_Select extends \Glue\DB\Fragment_Item {
	/**
	 * @var \Glue\DB\Fragment Fragment to be selected.
	 */
	protected $selected;

	/**
	 * @var string Alias.
	 */
	protected $alias;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $selected
	 * @param string $alias
	 */
	public function __construct(\Glue\DB\Fragment $selected, $alias) {
		$this->selected($selected);
		$this->alias($alias);
	}

	/**
	 * Selected fragment getter/setter.
	 *
	 * @param \Glue\DB\Fragment $selected
	 *
	 * @return mixed
	 */
	public function selected(\Glue\DB\Fragment $selected = null) {
		if (func_num_args() === 0)
			return $this->selected;
		else
			return $this->selected = $selected;
	}

	/**
	 * Alias getter/setter.
	 *
	 * @param string $alias
	 *
	 * @return mixed
	 */
	public function alias($alias = null) {
		if (func_num_args() === 0)
			return $this->alias;
		else
			return $this->alias = $alias;
	}
}