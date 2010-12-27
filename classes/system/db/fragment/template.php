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
			// Locate columns identifiers and remove old dependencies :
			if (preg_match_all('/@\d+@/', $this->template, $matches))
				foreach($matches as $id)
					\Glue\DB\Fragment_Column::get($id)->unregister_user($this);

			// Replace template :
			$this->set_property('template', $template);
			
			// Locate columns identifiers and add new dependencies :
			if (preg_match_all('/@\d+@/', $this->template, $matches))
				foreach($matches as $id)
					\Glue\DB\Fragment_Column::get($id)->register_user($this);
					
			// Return $this for chainability :
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
			// Unregister old replacements :
			foreach($this->replacements as $replacement)
				$replacement->unregister_user($this);
					
			// Turn replacements that aren't fragments into value fragments (SQL = quoted value) :
			$new = array();
			foreach($replacements as $replacement)
				$new[] = $replacement instanceof \Glue\DB\Fragment ? $replacement :	new \Glue\DB\Fragment_Value($replacement);
			$replacements = $new;
				
			// Replace replacements :
			$this->set_property('replacements', $replacement);
				
			// Register new replacements :
			foreach($this->replacements as $replacement)
				$replacement->register_user($this);
				
			// Return $this for chainability :
			return $this;
		}
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
		// Replace column ids by columns SQL in template :
		$template = preg_replace_callback(
			'/@\d+@/',
			function ($matches) use ($cn, $style) { // closure
				$id = $matches[0];
				return \Glue\DB\Fragment_Column::get($matches[0])->sql($cn, $style);
			},
			$this->template
		);
		
		// Make replacements :
		$sql = '';
		foreach (explode('?', $template) as $index => $part)
			$sql .= $part . (isset($replacements[$index]) ? $replacements[$index]->sql($cn, $style) : '');
			
		return $sql;
	}
}