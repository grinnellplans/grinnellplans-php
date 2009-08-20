<?php
require_once('bootstrap.php');

if (!defined('ENVIRONMENT')) {
	define('ENVIRONMENT', 'production');
}

if (ENVIRONMENT == 'development') {
	ini_set('error_reporting', E_ALL | E_STRICT);
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
if (User::logged_in() && ENVIRONMENT == 'testing' && !$_SESSION['accept_beta'] && basename($_SERVER['PHP_SELF']) != 'beta_warning.php') {
	Redirect('beta_warning.php');
	return;
}
?>
