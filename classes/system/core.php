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
		spl_autoload_register('\Glue\Core::auto_load');
	}

	/**
	 * Auto-loader for classes under the Glue namespace.
	 *
	 * @link http://rlm80.github.com/Glue/file_system.html
	 *
	 * @param string $class
	 */
	static public function auto_load($class) {
		// Uncapitalize class name :
		$class = strtolower($class);

		// Only load classes in the Glue namespace :
		if (substr($class, 0, 5) !== 'glue\\') return;

		// Load user and system classes :
		if (substr($class, 0, 12) === 'glue\\system\\' || substr($class, 0, 10) === 'glue\\user\\') {
			// Init variables :
			list($ns, $sns, $rest) = explode('\\', $class, 3);

			// Build file path :
			if ($sns === 'system')
				$path = \Glue\CLASSPATH_SYSTEM;
			else
				$path = \Glue\CLASSPATH_USER;
			$path .= str_replace(array('_','\\'), '/', $rest) . '.php';

			// Include class file if it exists :
			if (is_file($path)) include $path;

			// Return :
			return;
		}

		// Set up alias :
		$rest			= substr($class, 5);
		$class_user		= '\\Glue\\User\\'   . $rest;
		$class_system	= '\\Glue\\System\\' . $rest;
		if (class_exists($class_user, true))
			class_alias($class_user, $class);
		elseif (class_exists($class_system, true))
			class_alias($class_system, $class);

		// Return :
		return;
	}

	static public function gendoc() {
		// Init doc file with new namespace :
		//$doc = "<?php\n\nnamespace $namespace;\n\n";
	}

	/**
	 * Returns classes skeletons for all class files found in given path.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	static protected function get_doc($path) {
		// Get all php files in path (including subdirectories) :
		$files = self::globr($path, '*.php');

		// Loop on each file and generate classes skeletons :
		$doc = '';
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
			$doc .= "\n\n" . $content;
		}

		return $doc;
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
	static protected function globr($dir, $pattern, $flags = null) {
	  $files = glob("$dir/$pattern", $flags);
	  foreach (glob("$dir/*", GLOB_ONLYDIR) as $subDir) {
		$subFiles = self::globr($subDir, $pattern, $flags);
		$files = array_merge($files, $subFiles);
	  }
	  return $files;
	}
}