<?php

namespace Glue;

/**
 * Glue bootstrap file.
 *
 * This file must be included by the application before any attempt to use the library. It sets up
 * the auto-loader and some path constants.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

	// Define path constants :
	define('Glue\\ROOTPATH', __DIR__ . '/');
	define('Glue\\CACHEPATH', ROOTPATH . 'cache/');
	define('Glue\\CLASSPATH_SYSTEM', ROOTPATH . 'classes/system/');
	define('Glue\\CLASSPATH_USER', ROOTPATH . 'classes/user/');

	// Require Core class :
	if (is_file(CLASSPATH_USER . 'core.php')) {
		require CLASSPATH_USER . 'core.php';
		class_alias('Glue\\User\\Core', 'Glue\\Core');
	}
	else {
		require CLASSPATH_SYSTEM . 'core.php';
		class_alias('Glue\\System\\Core', 'Glue\\Core');
	}

	// Bootstrap library :
	Core::bootstrap();