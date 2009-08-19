<?php
require_once('bootstrap.php');

if (ENVIRONMENT == 'dev') {
	//TODO For now, this is not a constant because I want to be able to change 
	// it, but there's probably a better solution in the long run
	$GLOBALS['ENVIRONMENT'] = 'development';
} else {
	$GLOBALS['ENVIRONMENT'] = 'production';
}

if ($GLOBALS['ENVIRONMENT'] == 'development') {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('html_errors', TRUE);
} else {
	ini_set('display_errors', FALSE);
}

// Simple functions
require_once('functions.php');

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
