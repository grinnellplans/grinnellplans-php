<?php
	require_once("Plans.php");

if (isset($_GET['jumbled'])) {
if($_GET['jumbled'] == 'no') {
	setcookie('jumbled', 'no');
}
if($_GET['jumbled'] == 'yes') {
	setcookie('jumbled', 'yes');
}
} else {
}
//Load Configuration settings - let's increase our level of abstraction here....
require_once("Configuration.php");

//Load the legal disclaimer file (Your needs may differ!)
require("legal.php");

//Load Plans database abstraction functions
//Warning: Still using old version.
require("dbfunctions.php");

//Load autofinger functions
require("functions-autofinger.php");

//Load user display functions
require("functions-display.php");

//Load Notes functionalities
require("functions-forum.php");

//Load valid-user authentications
require("functions-authentication.php");

//Load editing, updating, and other related functions
require("functions-edit.php");

require("functions-core.php");

require("core-perms.php");
?>