<?php
require_once ('bootstrap.php');
if (ENVIRONMENT == 'dev') {
    // Error reporting for development
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    ini_set('html_errors', TRUE);
    //TODO For now, this is not a constant because I want to be able to change
    // it, but there's probably a better solution in the long run
    $GLOBALS['ENVIRONMENT'] = 'development';
} else {
    // For production, be a bit loose.
    ini_set('track_errors', FALSE);
    $GLOBALS['ENVIRONMENT'] = 'production';
}

// Session setup
ini_set('session.use_strict_mode',true);
ini_set('session.gc_maxlifetime', COOKIE_EXPIRATION); // Used internally by the AWS session handler, so still works even if DynamoDB storage is enabled
ini_set('session.cookie_lifetime',COOKIE_EXPIRATION + 3600); //handle incorrect client clocks a bit better
ini_set('session.cookie_domain', COOKIE_DOMAIN);
if(defined('DDB_SESSION_TABLE')) {
$dynamoDb = new Aws\DynamoDb\DynamoDbClient(['region' => 'us-east-1', 'version' => '2012-08-10']);
$dynamoDb->registerSessionHandler(['table_name' => DDB_SESSION_TABLE]);
ini_set('session.gc_probability', 0); // Never garbage collect sessions - let AWS handle it
}
session_start();

// Simple functions
require_once ('functions-main.php');
new ResourceCounter();
header('Content-Type: text/html; charset=UTF-8');
// If we're on a testing environment, warn them
if (User::logged_in() && $GLOBALS['ENVIRONMENT'] == 'testing' && !$_SESSION['accept_beta'] && basename($_SERVER['PHP_SELF']) != 'beta_warning.php') {
    Redirect('beta_warning.php');
    return;
}
?>
