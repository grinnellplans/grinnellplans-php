<?php
define('__ROOT__', dirname(__FILE__));
require_once('Configuration.php');

ini_set('include_path', '.:' . __ROOT__ . ':' . __ROOT__ . '/inc');
putenv('TZ=' . TZ);

// Doctrine setup
require_once('lib/doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
Doctrine_Manager::getInstance()->setAttribute('model_loading', 'conservative');
Doctrine::loadModels(__ROOT__ . '/db/models'); // This call will not require the found .php files
Doctrine_Manager::connection(DB_URI);

// Autoloader for classes
function plans_autoload($classname) {
	$filename = str_replace('_', '/', $classname);
	if (is_readable(__ROOT__ . "/inc/$filename.php")) {
		require_once(__ROOT__ . "/inc/$filename.php");
	}
}
spl_autoload_register('plans_autoload');

// Convert all errors to exceptions if desired
if (STRICT_EXCEPTIONS === true) {
	function exception_error_handler($errno, $errstr, $errfile, $errline) {
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	set_error_handler("exception_error_handler");
}
