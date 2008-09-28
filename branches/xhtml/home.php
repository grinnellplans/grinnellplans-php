<?php
require_once ("Plans.php");
require ("functions-main.php");
require_once("syntax-classes.php");
$dbh = db_connect();


// Create the new page
$page = new PlansPage('Plan', 'readplan', PLANSVNAME, 'read.php');
if (User::logged_in()) {
	get_interface($idcookie);
	populate_page($page, $dbh, $idcookie);
	$my_result = mysql_query("Select system.motd,accounts.spec_message From
			system,accounts where accounts.userid = '$idcookie'"); //get the main plans messsage as well as the person's private message to be displayed
	$my_row = mysql_fetch_array($my_result); //get information from mysql query
	$privmessage = new InfoText(stripslashes(stripslashes($my_row[1])), 'User MOTD'); //if logged in, show the private message
	$page->append($privmessage);

} else {
	get_guest_interface();
	populate_guest_page($page);
	$dbh = db_connect(); //sets up connection to database.
	$my_result = mysql_query("Select system.motd From system"); //get the main plans message from the database
	$my_row = mysql_fetch_array($my_result); //get information from mysql query
}

$motd = new InfoText(stripslashes(stripslashes($my_row[0])), 'MOTD'); //display the main Plans message
$page->append($motd);
interface_disp_page($page);

db_disconnect($dbh);
?>