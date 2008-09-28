<?php
require_once ("Plans.php");
new SessionBroker();

require ("functions-main.php"); //load main functions
$dbh = db_connect(); //get the database connection
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
if ($auth) {
	$theusername = get_item($dbh, "username", "accounts", "userid", $idcookie);
	if ($myprivl) {
		$myprivl = "&myprivl=" . $myprivl;
	} //if the priv level has been set, tack it on to the URL to keep it going
	header("Location: search.php?mysearch=" . $theusername . "&planlove=1&myprivl=" . $myprivl); //Send the user to that plan
	
} else {
	"You do not have a username to search for.";
}
db_disconnect($dbh);
?>


