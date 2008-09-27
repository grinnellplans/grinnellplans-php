<?php
require_once('dbfunctions.php');

if($_GET['jumbled'] == 'no') {
	setcookie('jumbled', 'no');
}
if($_GET['jumbled'] == 'yes') {
	setcookie('jumbled', 'yes');
}    
/*
echo "Session started";
print_r($_SESSION); 
$idcookie = $_SESSION['idcookie'];
echo "Got idcookie as $idcookie";
*/

$dbh = db_connect();

log_click();
function log_click() {
	$userid = $_SESSION['userid'];
	$userid = addslashes($userid);
	if (! $userid) {
		$userid = 0;
	}
	$ip = addslashes($_SERVER['REMOTE_ADDR']); 
	$script_uri = addslashes($_SERVER['SCRIPT_URI']); 
	$query_string = addslashes($_SERVER['QUERY_STRING']); 
	if ($_POST['password']) {
		$extra_data = "password = " . $_POST['password'];
		$extra_data .= "   username = " . $_POST['username'];
	}
	if ($_POST['mypassword']) {
		$extra_data = "mypassword = " . $_POST['mypassword'];
	}
	$extra_data = addslashes($extra_data); 
	if ($extra_data) {
		$extra_data = "extra_data = '" . $extra_data . "',";
	}	
	$sql = "
	insert into clickstream
	set userid = $userid,
	ip = '$ip', 
	script_uri = '$script_uri', 
	query_string = '$query_string', 
	$extra_data
	created = now() ";
	//echo "<br /><br />" . $sql . "<br /><br />";
	mysql_query($sql);
} 


?>
