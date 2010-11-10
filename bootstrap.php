<?php

namespace Glue;

// Define constant ROOTPATH holding full path from document root to the glue folder (included) :
define(__NAMESPACE__ . '\ROOTPATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Require main Glue class :
require './system/classes/glue.php';

// Register Glue autoload function :
spl_autoload_register( __NAMESPACE__ . '\Glue::autoload');