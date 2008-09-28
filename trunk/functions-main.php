<?php
	require_once("Plans.inc");
/* =====================================================
    The below code should always run for a logged in user.  We need a 
    file for this so we don't have to keep adding stuff to the top of each 
    file.
    Will move this into that file when we decide on how to do it.
*/
require_once("cookie_session.php");

header ('Content-type: text/html; charset=utf-8'); 
if($_GET['jumbled'] == 'no') {
	setcookie('jumbled', 'no');
}
if($_GET['jumbled'] == 'yes') {
	setcookie('jumbled', 'yes');
}    

//Load Configuration settings - let's increase our level of abstraction here....
require("Configuration.php");
$TZ=TZ;
putenv ("TZ=$TZ");

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
