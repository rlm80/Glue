<?php

namespace Glue\System;

/**
 * Core Glue class.
 *
 * Auto-loading of classes and utility functions.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

class Core {
	/**
	 * First function executed when bootstrap.php is included. It registers the auto-loader. You may
	 * want to override this to do other things of your own.
	 */
	static public function bootstrap() {
		// Register autoloader :
		spl_autoload_register('\Glue\Core::load_class');
	}

	/**
	 * For given unknown class identifier, does what's necessary to make it known to PHP. Returns true if we
	 * found a way to do that, false otherwise.
	 *
	 * @see http://rlm80.github.com/Glue/file_system.html
	 *
	 * @param string $class
	 *
	 * @return boolean
	 */
	static public function load_class($class) {
		// Uncapitalize class name :
		$class = strtolower($class);

		// Only deal with class names in the Glue namespace :
		if (substr($class, 0, 5) !== 'glue\\') return FALSE;

		// Load user and system classes :
		if (preg_match('`^glue\\\\(system|user)\\\\(db|orm)\\\\([^\\\\]*)$`', $class, $matches)) {
			// Build path where the class is supposed to be located :
			$path = ($matches[1] === 'system' ? \Glue\CLASSPATH_SYSTEM : \Glue\CLASSPATH_USER) . $matches[2] . '/' . str_replace('_', '/', $matches[3]) . '.php';

			// Check if such a file exists and include it :
			if (is_file($path)) {
				include $path;
				return TRUE;
			}
			else
				return FALSE;
		}

		// Load alias :
		if (preg_match('`^glue\\\\((db|orm)\\\\[^\\\\]*)$`', $class, $matches)) {
			// Attempt alias to user class :
			if (class_exists($original = 'Glue\\User\\' . $matches[1], true))
				return class_alias($original, $class);

			// Attempt alias to system class :
			if (class_exists($original = 'Glue\\System\\' . $matches[1], true))
				return class_alias($original, $class);
		}

		return FALSE;
	}

	/**
	 * Generates auto-complete file for IDE at the appropirate location.
	 */
	static public function gendoc() {
		// Init doc file :
		$doc = "<?php \n\n";

		// Generate DB docs :
		$classes_system	= static::get_doc(\Glue\CLASSPATH_SYSTEM	. 'db');
		$classes_user	= static::get_doc(\Glue\CLASSPATH_USER		. 'db');
		$classes		= array_merge($classes_system, $classes_user);
		$doc .= "namespace Glue\DB {\n\n";
		$doc .= implode("\n\n", $classes);
		$doc .= "\n\n}\n\n";

		// Generate ORM docs :
		$classes_system	= static::get_doc(\Glue\CLASSPATH_SYSTEM	. 'orm');
		$classes_user	= static::get_doc(\Glue\CLASSPATH_USER		. 'orm');
		$classes		= array_merge($classes_system, $classes_user);
		$doc .= "namespace Glue\ORM {\n\n";
		$doc .= implode("\n\n", $classes);
		$doc .= "\n\n}\n\n";

		// Save docs :
		file_put_contents(\Glue\ROOTPATH . 'ide/autocomplete.php', $doc);
	}

	/**
	 * Returns class skeletons for all class files found in given path, as an array indexed by class name.
	 *
	 * TODO change this, and store everything as separate files + only system files ?
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	static protected function get_doc($path) {
		// Get all php files in path (including subdirectories) :
		$files = self::globr($path, '*.php');

		// Loop on each file and generate classes skeletons :
		$docs = array();
		foreach($files as $file) {
			// Get file content :
			$content = file_get_contents($file);

			// Get namespace :
			if(preg_match('/^\s*namespace\s+([^;]+);/', $content, $matches))
				$ns = $matches[1];
			else
				$ns = '';

			// Get class name :
			if(preg_match('/\sclass\s+(\w+)/', $content, $matches))
				$class = $matches[1];
			else
				$class = '';

			// Remove opening php tags :
			$content = preg_replace('/^\s*<\?php\s*/', '', $content);

			// Remove namespace declaration :
			$content = preg_replace('/^\s*namespace\s+[^;]+;/', '', $content);

			// Remove functions bodies :
			$content = preg_replace('/(function\s+\w+\s*\([^)]*\))\s*({((?>[^{}]+)|(?2))*})/sm', '\1 {}', $content);

			// Adds @see to phpdoc that links to the original function in the original namespace :
			$content = preg_replace('`/\*\*([^{};]*?\*/[^{};]*?\sfunction\s+(\w+))`sm', '/** @see \\' . $ns . '\\' . $class . '::\2()' . "\n" . ' \1', $content);

			// Append class skeleton to doc :
			$docs[$class] = $content;
		}

		return $docs;
	}

	/**
	 * Recursive version of glob.
	 *
	 * @param string $dir      Directory to start with.
	 * @param string $pattern  Pattern to glob for.
	 * @param int $flags       Flags sent to glob.
	 *
	 * @return array containing all pattern-matched files.
	 */
	static public function globr($dir, $pattern, $flags = null) {
	  $files = glob("$dir/$pattern", $flags);
	  foreach (glob("$dir/*", GLOB_ONLYDIR) as $subDir) {
		$subFiles = self::globr($subDir, $pattern, $flags);
		$files = array_merge($files, $subFiles);
	  }
	  return $files;
	}

	/**
	 * Returns cached object on $path in the cache directory. Returns null if no cache entry at $path.
	 *
	 * @param string $path Path to cache file, relative to cache directory.
	 *
	 * @return mixed
	 */
	static public function get_cache_entry($path) {
		$abspath = \Glue\CACHEPATH . $path;
		if (file_exists($abspath))
			return unserialize(file_get_contents($abspath));
		else
			return null;
	}

	/**
	 * Creates new cache entry for $object on $path in cache directory.
	 *
	 * @param string $path Path to cache file.
	 * @param mixed $object Object to store in cache.
	 */
	static public function create_cache_entry($path, $object) {
		$parts	= explode('/', $path);
		$file	= array_pop($parts);
		$dir	= \Glue\CACHEPATH . implode('/', $parts);
		if ( ! is_dir($dir)) mkdir($dir, 777, true);
		file_put_contents($dir . '/' . $file, serialize($object));
	}

	/**
	 * Clears cache.
	 *
	 * @param string $path Cache subdirectory to delete.
	 */
	static public function clear_cache($path = '') {
		static::rrmdir(\Glue\CACHEPATH . $path);
	}

	/**
	 * Recursively deletes a directory and its content.
	 *
	 * @link http://be.php.net/manual/en/function.unlink.php#79940
	 *
	 * @param $dir
	 */
	static public function rrmdir($dir, $DeleteMe = true) {
	    if(!$dh = @opendir($dir)) return;
	    while (false !== ($obj = readdir($dh))) {
	        if($obj=='.' || $obj=='..') continue;
	        if (!@unlink($dir.'/'.$obj)) static::rrmdir($dir.'/'.$obj, true);
	    }
	    closedir($dh);
	    if ($DeleteMe){
	        @rmdir($dir);
	    }
	}
}