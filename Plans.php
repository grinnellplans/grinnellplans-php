<?php
// Set some settings only for when we're called through /beta/.
if ((strstr($_SERVER['REQUEST_URI'], '/dev/') != FALSE) ||
	($_SERVER['SERVER_NAME'] == 'dev.grinnellplans.com') ||
 	(strstr($_SERVER['SERVER_NAME'], 'localhost') != FALSE) ||
	(strstr($_SERVER['REQUEST_URI'], '/trunk/') != FALSE)) {
	// Error reporting for development
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('html_errors', TRUE);

	// Be very strict with globals
	ini_set('register_globals', FALSE);
	ini_set('register_long_arrays', FALSE);
	ini_set('register_argc_argv', FALSE);
} else {
	// For production, be a bit loose.
	ini_set('register_globals', TRUE);
	ini_set('register_long_arrays', TRUE);
	ini_set('register_argc_argv', TRUE);

	ini_set('track_errors', FALSE);
}

// Boilerplate code for _all_ Plans scripts
define('__ROOT__', dirname(__FILE__));
require_once('Configuration.php');
ini_set('include_path', '.:' . __ROOT__ . ':' . __ROOT__ . '/inc');
putenv('TZ=' . TZ);

// Doctrine setup
require_once('lib/doctrine/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
Doctrine_Manager::getInstance()->setAttribute('model_loading', 'conservative');
Doctrine::loadModels(__ROOT__ . '/db'); // This call will not require the found .php files
Doctrine_Manager::connection(DB_URI);

// Simple functions
require_once('functions.php');

// Autoloader for classes
function plans_autoload($classname) {
	$filename = str_replace('_', '/', $classname);
	require_once("$filename.php");
}
spl_autoload_register('plans_autoload');

new ResourceCounter();
new SessionBroker();
new ClickstreamEvent();
header('Content-Type: text/html; charset=UTF-8');
?>
