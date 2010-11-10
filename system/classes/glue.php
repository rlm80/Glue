<?php

namespace Glue;

/**
 * Main Glue class.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Glue {
	static function autoload($class) {
		// Abstract path in merged system/user file system :
		$abstract_path = str_replace('_', DIRECTORY_SEPARATOR, strtolower($class));

		// Concrete path on disk file system :
		$concrete_path = self::concrete_path($abstract_path);

		// Load file :
		if ($concrete_path !== FALSE) require $concrete_path;
	}

	/**
	 * Given an abstract path in the merged system/user file system, returns the concrete path
	 * on the disk file system. Returns false if file not found.
	 *
	 * @param string $abstract_path
	 *
	 * @return string
	 */
	static function concrete_path($abstract_path) {
		// Look for file in user file system :
		$path = ROOTPATH . 'user' . DIRECTORY_SEPARATOR . $abstract_path;
		if (is_file($path)) return $path;

		// Look for file in system file system :
		$path = ROOTPATH . 'system' . DIRECTORY_SEPARATOR . $abstract_path;
		if (is_file($path)) return $path;

		return FALSE;
	}
}