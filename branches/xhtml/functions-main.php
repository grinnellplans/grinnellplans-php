<?php
/*
Identification: GrinnellPlans - plansfunctions files
Version: Nov-11-05-1 (Laiu-Draft-1)
What this does: It replaces KenslerJ-2 functions.php and divides up the functions code by purpose.
Ideally, this makes rewriting Plans easier.
Notes: This file will contain functions called up by GrinnellPlans
*/
/* =====================================================
The below code should always run for a logged in user.  We need a
file for this so we don't have to keep adding stuff to the top of each
file.
Will move this into that file when we decide on how to do it.
*/
header('Content-type: text/html; charset=utf-8');
if ($_GET['jumbled'] == 'no') {
	setcookie('jumbled', 'no');
}
if ($_GET['jumbled'] == 'yes') {
	setcookie('jumbled', 'yes');
}
//Load Configuration settings - let's increase our level of abstraction here....
require ("config.php");
//Load Plans database abstraction functions
//Warning: Still using old version.
require ("dbfunctions.php");
//Load autofinger functions
require ("functions-autofinger.php");
//Load global variables
require ("globals.php");
//Load user display functions
require ("functions-display.php");
//Load Notes functionalities
require ("functions-forum.php");
//Load valid-user authentications
require ("functions-authentication.php");
//Load editing, updating, and other related functions
require ("functions-edit.php");
require ("functions-core.php");
require ("functions-transition.php");
require ("core-perms.php");
?>
