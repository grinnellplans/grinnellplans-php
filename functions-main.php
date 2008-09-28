<?php
require_once ("Plans.php");
if (isset($_GET['jumbled'])) {
	if ($_GET['jumbled'] == 'no') {
		setcookie('jumbled', 'no');
	}
	if ($_GET['jumbled'] == 'yes') {
		setcookie('jumbled', 'yes');
	}
} else {
}

//Load Configuration settings - let's increase our level of abstraction here....
require_once ("Configuration.php");
//Load the legal disclaimer file (Your needs may differ!)
require_once ("legal.php");
//Load Plans database abstraction functions
//Warning: Still using old version.
require_once ("dbfunctions.php");
//Load autofinger functions
require_once ("functions-autofinger.php");
//require_once user display functions
require_once ("functions-display.php");
//Load Notes functionalities
require_once ("functions-forum.php");
//Load valid-user authentications
require_once ("functions-authentication.php");
//Load editing, updating, and other related functions
require_once ("functions-edit.php");
require_once ("functions-core.php");
require ("core-perms.php");
?>