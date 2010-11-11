<?php

namespace Glue;

/**
 * Main Glue class.
 * 
 * Utility functions for the file system and auto-loading of classes.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Glue {
	/**
	 * @var array Config options.
	 */
	static $config = array();
	
	/**
	 * Auto-loader for classes under the Glue namespace.
	 * 
	 * All classes in the Glue namespace are expected to follow a \Glue\<subnamespace>\<class name with underscores>
	 * naming scheme.
	 * 
	 * Expected class locations are as follows (by priority order) :
	 *   1) Glue folder / user   / classes / <subnamespace> / + subfolders and file name according to '_' in class name
	 *   2) Glue folder / system / classes / <subnamespace> / + subfolders and file name according to '_' in class name
	 * 
	 * @param string $class
	 */
	static public function auto_load($class) {
		$class = strtolower($class);
		
		// Break fully qualified class name apart into namespaces components and name :
		$parts = explode('\\', $class);
		
		// Only load classes made of a namespace, a sub-namespace, and a class name :
		if (count($parts) !== 3) return;
		list($namespace, $subnamespace, $name) = $parts;
		
		// Only load classes of Glue namespace :
		if ($namespace !== 'glue') return;
		
		// Explode name :
		$parts = explode('_', $name);
		
		// Abstract path in file system :
		$path = 'classes/' . $subnamespace . '/' . implode('/', $parts) . '.php';

		// Concrete path in file system :
		$realpath = self::find_file($path);

		// Load file :
		if ($realpath !== FALSE) require $realpath;
	}

	/**
	 * Given an abstract path in the merged system/user file system, returns the concrete path
	 * of the file on the disk file system. Returns false if file not found.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	static public function find_file($path) {
		// Look for file in user file system :
		$realpath = ROOTPATH . 'user/' . $path;
		if (is_file($realpath)) return $realpath;

		// Look for file in system file system :
		$realpath = ROOTPATH . 'system/' . $path;
		if (is_file($realpath)) return $realpath;

		return FALSE;
	}
	
	/**
	 * Config option setter.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	static public function option_set($key, $value) {
		self::$config[$key] = $value;
	}
	
	/**
	 * Checks existence of a key in the config array. 
	 * 
	 * @param string $key
	 * 
	 * @return boolean
	 */
	static public function option_exists($key) {
		return array_key_exists($key, self::$config);
	}		
	
	/**
	 * Config option getter.
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 */
	static public function option_get($key) {
		if ( ! self::option_exists($key))
			throw new Exception("Glue option " . $key . " is not defined.");
		return self::$config[$key];
	}	
}