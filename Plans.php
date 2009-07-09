<?php
// Set some settings only for when we're called through /beta/.
if ((strstr($_SERVER['REQUEST_URI'], '/dev/') != FALSE) ||
	($_SERVER['SERVER_NAME'] == 'dev.grinnellplans.com') ||
 	(strstr($_SERVER['SERVER_NAME'], 'localhost') != FALSE) ||
	(strstr($_SERVER['REQUEST_URI'], '/trunk/') != FALSE)) {
	//TODO For now, this is not a constant because I want to be able to change 
	// it, but there's probably a better solution in the long run
	$GLOBALS['ENVIRONMENT'] = 'development';
} else {
	$GLOBALS['ENVIRONMENT'] = 'production';
}

// Be very strict with globals
ini_set('register_globals', FALSE);
ini_set('register_long_arrays', FALSE);
ini_set('register_argc_argv', FALSE);

// Boilerplate code for _all_ Plans scripts
define('__ROOT__', dirname(__FILE__));
require_once('Configuration.php');
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

// Plans Revision
if (file_exists(__ROOT__ . '/.svn/entries')) {
    $svn = file(__ROOT__ . '/.svn/entries');
    if (is_numeric(trim($svn[3]))) {
        $version = $svn[3];
    } else { // pre 1.4 svn used xml for this file
        $version = explode('"', $svn[4]);
        $version = $version[1];    
    }
    define ('PLANS_REVISION', trim($version));
    unset ($svn);
    unset ($version);
} else {
    define ('PLANS_REVISION', 0); // default if no svn data avilable
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

new ResourceCounter();
new SessionBroker();
new ClickstreamEvent();
header('Content-Type: text/html; charset=UTF-8');

// If we're on a testing environment, warn them
if (User::logged_in() && $GLOBALS['ENVIRONMENT'] == 'testing' && !$_SESSION['accept_beta'] && basename($_SERVER['PHP_SELF']) != 'beta_warning.php') {
	Redirect('beta_warning.php');
	return;
}
?>
