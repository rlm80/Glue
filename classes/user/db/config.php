<?php

namespace Glue\User\DB;

/**
 * This class overrides the \Glue\System\DB\Config class. You can set the options in the constructor. If you're
 * using an IDE, just type "$this->" in there and the auto-complete system will show you all available options.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Config extends \Glue\System\DB\Config {
	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct();

		// Set options below :
		//$this->myoption1 = 'my value 1';
		//$this->myoption2 = 'my value 2';
		//...
	}
}