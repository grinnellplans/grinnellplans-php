<?php
define('__ROOT__', dirname(__FILE__));
require_once('Configuration.php');

if (ENVIRONMENT == 'dev') {
	//TODO For now, this is not a constant because I want to be able to change 
	// it, but there's probably a better solution in the long run
	$GLOBALS['ENVIRONMENT'] = 'development';
} else {
	$GLOBALS['ENVIRONMENT'] = 'production';
}

ini_set('include_path', '.:' . __ROOT__ . ':' . __ROOT__ . '/inc');
putenv('TZ=' . TZ);

if ($GLOBALS['ENVIRONMENT'] == 'development') {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('html_errors', TRUE);
} else {
	ini_set('display_errors', FALSE);
}

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

require_once('functions-main.php');

new ResourceCounter();
new SessionBroker();
header('Content-Type: text/html; charset=UTF-8');

// If we're on a testing environment, warn them
if (User::logged_in() && $GLOBALS['ENVIRONMENT'] == 'testing' && !$_SESSION['accept_beta'] && basename($_SERVER['PHP_SELF']) != 'beta_warning.php') {
	Redirect('beta_warning.php');
	return;
}
?>
