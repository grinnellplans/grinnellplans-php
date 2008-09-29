<?php
require_once ("Plans.php");
require ("functions-main.php");

$dbh = db_connect();
$idcookie = User::id();
$myprivl = setpriv($myprivl, $HTTP_COOKIE_VARS["thepriv"]);

if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl); //send beginning display info
} else {
	gdisp_begin($dbh);
}

$my_result = mysql_query("Select system.motd from system");
$my_row = mysql_fetch_array($my_result); //get information from mysql query
// echo stripslashes(stripslashes($my_row[1])); //if logged in, show the private message
echo '<pre>';
echo '</pre>';

echo stripslashes(stripslashes($my_row[0])); //display the main Plans message

if (User::logged_in()) {
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl); //and send closing display data
} else {
	gdisp_end();
}


db_disconnect($dbh);
?>