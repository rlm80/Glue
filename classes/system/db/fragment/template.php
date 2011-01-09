<?php

namespace Glue\System\DB;

/**
 * Fragment that represents a pseudo-SQL template to be included in the query.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Template extends \Glue\DB\Fragment {
	/**
	 * @var string Pseudo-SQL template.
	 */
	protected $template;

	/**
	 * @var array Replacements to be made in pseudo-SQL template.
	 */
	protected $replacements = array();

	/**
	 * Constructor.
	 *
	 * @param string $template
	 * @param array $replacements
	 */
	public function __construct($template = '', array $replacements = array()) {
		$this->template($template);
		$this->replacements($replacements);
	}

	/**
	 * Template setter/getter.
	 *
	 * @param string $template
	 *
	 * @return mixed
	 */
	public function template($template = null) {
		if (func_num_args() === 0)
			return $this->template;
		else {
			$this->template = $template;
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