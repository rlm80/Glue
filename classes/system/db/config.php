<?php

namespace Glue\System\DB;

/**
 * Config class.
 *
 * Config options are stored as properties of a singleton instance of this class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class Config {
	/*--------------------------------  OPTIONS  ------------------------------------*/

	/**
	 * @var array Connection ids => database classes mapping.
	 * @see \Glue\DB\DB::db()
	 */
	protected $connections = array('default' => 'Glue\\DB\\Database_Mysql_default');

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
	 * Returns database classes array.
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

