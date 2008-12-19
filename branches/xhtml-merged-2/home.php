<?php
require_once('Plans.php');
require ("functions-main.php");
require ('syntax-classes.php');

$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Main', 'home', PLANSVNAME, 'home.php');

if (User::logged_in()) {
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);
} else {
	get_guest_interface();
	populate_guest_page($thispage);
}

$my_result = mysql_query("Select system.motd from system");
$my_row = mysql_fetch_array($my_result); //get information from mysql query
// echo stripslashes(stripslashes($my_row[1])); //if logged in, show the private message

$motd = new PlanText(stripslashes(stripslashes($my_row[0])), false); //display the main Plans message
$thispage->append($motd);

interface_disp_page($thispage);
db_disconnect($dbh);
?>
