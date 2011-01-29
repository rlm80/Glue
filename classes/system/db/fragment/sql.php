<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a pseudo-SQL template to be included in the query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_SQL extends \Glue\DB\Fragment {
	/**
	 * @var string Pseudo-SQL template.
	 */
	protected $sql;

	/**
	 * @var array Replacements to be made in pseudo-SQL template.
	 */
	protected $replacements = array();

	/**
	 * Constructor.
	 *
	 * @param string $sql
	 * @param array $replacements
	 */
	public function __construct($sql = '', array $replacements = array()) {
		$this->sql($sql);
		$this->replacements($replacements);
	}

	/**
	 * Template setter/getter.
	 *
	 * @param string $sql
	 *
	 * @return mixed
	 */
	public function sql($sql = null) {
		if (func_num_args() === 0)
			return $this->sql;
		else {
			$this->sql = $sql;
			return $this;
		}
	}

	/**
	 * Replacements setter/getter.
	 *
	 * @param array $replacements
	 *
	 * @return mixed
	 */
	public function replacements(array $replacements = array()) {
		if (func_num_args() === 0)
			return $this->replacements;
		else {
			$this->replacements = $replacements;
			return $this;
		}
	}
}