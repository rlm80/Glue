<?php

namespace Glue;

/**
 * Glue bootstrap file. 
 * 
 * This file must be included by the application before any attempt to use the library. It sets up
 * the auto-loader, path constants and default options values.
 *
 * @package Glue
 * @author Régis Lemaigre
 * @license MIT
 */

// Define constant ROOTPATH holding absolute path to the glue folder (included) :
define(__NAMESPACE__ . '\ROOTPATH', __DIR__ . '/');

// Require main Glue class :
require ROOTPATH . 'system/classes/glue.php';

// Register Glue autoload function :
spl_autoload_register( __NAMESPACE__ . '\Glue::auto_load');

// Execute config file :
require Glue::find_file('config.php');