<?php

namespace Glue\System\DB;

/**
 * Fragment that represents an SQL template to be included as-is in the query.
 * 
 * The template may have '?' placeholders and you may supply to the constructor an array of fragments to be
 * used for replacements.
 * 
 * The SQL output of a template fragment is the template, where each placeholder has been replaced by the SQL
 * output of each replacement fragment. If constant values are supplied for replacements, they will be turned
 * into Value fragments and thus quoted appropriately. 
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Fragment_Template extends \Glue\DB\Fragment {
	/**
	 * @var string SQL template with placeholders for values that need to be quoted.
	 */
	protected $template;

	/**
	 * @var array Replacements to be made in SQL template.
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