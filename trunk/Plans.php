<?php
require_once ('bootstrap.php');
if (ENVIRONMENT == 'dev') {
    // Error reporting for development
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    ini_set('html_errors', TRUE);
    // Be very strict with globals
    ini_set('register_globals', FALSE);
    ini_set('register_long_arrays', FALSE);
    ini_set('register_argc_argv', FALSE);
    //TODO For now, this is not a constant because I want to be able to change
    // it, but there's probably a better solution in the long run
    $GLOBALS['ENVIRONMENT'] = 'development';
} else {
    // For production, be a bit loose.
    ini_set('register_globals', TRUE);
    ini_set('register_long_arrays', TRUE);
    ini_set('register_argc_argv', TRUE);
    ini_set('track_errors', FALSE);
    $GLOBALS['ENVIRONMENT'] = 'production';
}
// Turn off magic quotes if they're on
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

ini_set('short_open_tag', FALSE); // Has no effect after PHP 4.0.0

// Simple functions
require_once ('functions-main.php');
new ResourceCounter();
new SessionBroker();
header('Content-Type: text/html; charset=UTF-8');
// If we're on a testing environment, warn them
if (User::logged_in() && $GLOBALS['ENVIRONMENT'] == 'testing' && !$_SESSION['accept_beta'] && basename($_SERVER['PHP_SELF']) != 'beta_warning.php') {
    Redirect('beta_warning.php');
    return;
}
?>
