<?php
// Boilerplate code for _all_ Plans scripts
require_once('Configuration.php');
putenv('TZ=' . TZ);

// Set some testings only for when we're called through /beta/.
if ((strstr($_SERVER['REQUEST_URI'], '/dev/') != FALSE) ||
 	(strstr($_SERVER['SERVER_NAME'], 'localhost') != FALSE) ||
	(strstr($_SERVER['REQUEST_URI'], '/trunk/') != FALSE)) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('html_errors', TRUE);
	
	ini_set('register_globals', FALSE);
	ini_set('register_long_arrays', FALSE);
	ini_set('register_argc_argv', FALSE);
}

ini_set('register_globals', TRUE);
ini_set('register_long_arrays', TRUE);
ini_set('register_argc_argv', TRUE);

ini_set('track_errors', FALSE);

// Doctrine setup
require_once('lib/doctrine/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
Doctrine_Manager::getInstance()->setAttribute('model_loading', 'conservative');
Doctrine::loadModels('db'); // This call will not require the found .php files
Doctrine_Manager::connection(DB_URI);

// Simple functions
require_once('functions.php');

function plans_autoload($classname) {
	require_once("inc/$classname.php");
}
spl_autoload_register('plans_autoload');

new ResourceCounter();
new SessionBroker();
new ClickstreamEvent();
header('Content-Type: text/html; charset=UTF-8');
?>
