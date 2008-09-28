<?php
/* Sechyi Laiu, Created 7/15/2005 12:58 p.m. 
 * Last edit -
 * Purpose: This program attempts to load up the 'home' page (MoTD under system table) upon request
 * Modified index.php from GrinnellPlans
 */

session_start();
//Load the functions file
require("functions-main.php");

//Connects to database and pulls out the information from cookies
$dbh = db_connect();//connect to database

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

//Check for user or NON-GUEST status, and provide correct display with MoTD
//This allows NON-GUESTS to read the MoTD
if ($auth)
{
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);
	$my_result = mysql_query("Select system.motd,accounts.spec_message From system,accounts where accounts.userid = '$idcookie'");
	$my_row = mysql_fetch_array($my_result);
	echo stripslashes(stripslashes($my_row[1]));
	echo stripslashes(stripslashes($my_row[0]));
}
	
else //begin ANYONE display
{
	gdisp_begin($dbh); //guest
	$my_result = mysql_query("Select system.motd From system"); //get the main plans message from the database
	$my_row = mysql_fetch_array($my_result); //get information from mysql query
	echo stripslashes(stripslashes($my_row[1]));
	echo stripslashes(stripslashes($my_row[0]));
}

//quick hack to display the public RSS page
//Disabled
//include("http://grinnellplans.com/rss");

db_disconnect($dbh);
?>
