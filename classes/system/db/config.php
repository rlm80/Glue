<?php

namespace Glue\System\DB;

/**
 * Config class.
 *
 * Config options are stored as properties of a singleton instance of this class.
 *
 * @package    Glue
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Config {
	/*--------------------------------  OPTIONS  ------------------------------------*/

	/**
	 * @var array Connection ids => connection classes mapping.
	 * @see \Glue\DB\DB::db()
	 */
	protected $connections = array('default' => 'Glue\\DB\\Connection_Mysql_default');

	/*-------------------------------  /OPTIONS  ------------------------------------*/

	/**
	 * @var \Glue\DB\Config Singleton instance.
	 */
	static protected $instance;

	/**
	 * Protected constructor.
	 */
	protected function __construct() {}

	/**
	 * Returns connections classes array.
	 */
	static public function connections() {
		return static::instance()->connections;
	}

	/**
	 * Creates and returns singleton instance.
	 *
	 * @return \Glue\DB\Config
	 */
	static protected function instance() {
		if ( ! isset(static::$instance))
			static::$instance = new static();
		return static::$instance;
	}
}

