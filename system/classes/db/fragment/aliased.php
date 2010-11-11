<?php

namespace Glue\DB;

/**
 * Fragment that represents anything that compiles into "... AS ...".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Aliased extends Fragment {
	/**
	 * @var Fragment Fragment that needs to have an alias.
	 */
	protected $aliased;

	/**
	 * @var string Alias.
	 */
	protected $as;

	/**
	 * Constructor.
	 *
	 * @param Fragment $aliased
	 * @param string $as
	 */
	public function __construct(Fragment $aliased, $as = null) {
		$this->aliased($aliased);
		$this->as($as);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param Fragment $aliased
	 *
	 * @return mixed
	 */
	public function aliased(Fragment $aliased = null) {
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
	 * Forwards call to given database.
	 *
	 * @param Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	function compile(Database $db, $style) {
		// Forwards call to database :
		return $db->compile_aliased($this, $style);
	}	
}