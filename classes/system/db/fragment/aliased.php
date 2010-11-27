<?php

namespace Glue\System\DB;

/**
 * Fragment that represents anything that compiles into "... AS ...".
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased extends \Glue\DB\Fragment {
	/**
	 * @var \Glue\DB\Fragment Fragment that needs to have an alias.
	 */
	protected $aliased;

	/**
	 * @var string Alias.
	 */
	protected $as;

	/**
	 * Constructor.
	 *
	 * @param \Glue\DB\Fragment $aliased
	 * @param string $as
	 */
	public function __construct(\Glue\DB\Fragment $aliased, $as = null) {
		$this->aliased($aliased);
		$this->as($as);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param \Glue\DB\Fragment $aliased
	 *
	 * @return mixed
	 */
	public function aliased(\Glue\DB\Fragment $aliased = null) {
		if (func_num_args() === 0)
			return $this->aliased;
		else
			return $this->set_property('aliased', $aliased);
	}

	/**
	 * Alias getter/setter.
	 *
	 * @param string $as
	 *
	 * @return mixed
	 */
	public function _as($as = null) {
		if (func_num_args() === 0)
			return $this->as;
		else
			return $this->set_property('as', $as);
	}

	/**
	 * Forwards call to given connection.
	 *
	 * @param \Glue\DB\Connection $cn
	 * @param integer $style
	 *
	 * @return string
	 */
	function compile(\Glue\DB\Connection $cn, $style) {
		// Forwards call to connection :
		return $cn->compile_aliased($this, $style);
	}
}