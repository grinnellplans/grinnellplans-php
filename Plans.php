<?php
	// Boilerplate code for _all_ Plans scripts

	ini_set('error_reporting', E_ALL);
	ini_set('register_globals',	FALSE);
	ini_set('html_errors',	TRUE);
	ini_set('include_path', '/inc');

	require_once("Configuration.php");	
	require_once("cookie_session.php");
	
	function __autoload($classname)
	{
		require_once("inc/$classname.php");
	}
	
	header('Content-Type: text/html; charset=UTF-8');
?>
