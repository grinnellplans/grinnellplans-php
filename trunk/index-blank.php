<?php
require_once ("Plans.php");
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