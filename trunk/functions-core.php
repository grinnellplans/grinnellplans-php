<?php
require_once ("Plans.php");
require_once ('dbfunctions.php');


$dbh = db_connect();
log_click();
function log_click()
{
	$userid = $_SESSION['userid'];
	$userid = addslashes($userid);
	if (!$userid) {
		$userid = 0;
	}
	$ip = addslashes($_SERVER['REMOTE_ADDR']);
	$script_uri = addslashes($_SERVER['SCRIPT_URI']);
	$query_string = addslashes($_SERVER['QUERY_STRING']);
	$sql = "
	insert into clickstream
	set userid = $userid,
	ip = '$ip', 
	script_uri = '$script_uri', 
	query_string = '$query_string',	created = now() ";
	mysql_query($sql);
}
?>