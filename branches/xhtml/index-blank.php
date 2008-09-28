<?php
/*
Identification: GrinnellPlans - index-blank.php
Version: 11-4-05-Laiu-Draft-1-1
What this does: This file returns a blank page to the person who changes a priority level immediately after login.
Without this page, the user would reload the login page and be forced to login again.
Notes: This is a legacy support page from kenslerj2 blank.php
*/
//Load main functions
require ("fuctions-main.php");
//Connect to database
$dbh = db_connect();
//Load the blank form page
//Get the cookie value/userID
list($idcookie, $password) = split("\|", $HTTP_COOKIE_VARS["idcookie"], 2);
//if logged in
if (isvalidauth($dbh, $idcookie, $password)) {
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
}
//Otherwise
else {
	echo "<html><body>Nothing to see here.</body></html>";
}
db_disconnect($dbh);
?>