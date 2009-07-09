<?php
require_once('Plans.php');

require_once ("dbfunctions.php");
$dbh = db_connect();

$userid = User::id();

$status = get_item($dbh, "status", "perms", "userid", $userid);
$write_only_access = array('edit.php' => 1, 'index.php' => 1, 'quicklove.php' => 1, 'search.php' => 1, 'home.php' => 1);
if ($status) {
	if ($status == 'write-only') {
		//		error_log("JLW: $userid, $status");
		$php_self = $_SERVER['PHP_SELF'];
		$php_self = preg_replace('/\//', '', $php_self);
		//		error_log("JLW: " . $php_self);
		if ($write_only_access[$php_self] && ($php_self != 'search.php' || $_GET['mysearch'] == $username)) {
			// Okay
			//			error_log("JLW: permitted");
			
		} else {
			echo 'Sorry, ' . $username . ' is a "write-only" plan, and as such does not have access to most Plans features out of respect for user privacy.';
			echo "<br />";
			echo "If it sounds like there's been some mistake, please email us at " . '<a href="mailto:grinnellplans@gmail.com">grinnellplans@gmail.com</a>.';
			//			error_log("JLW: blocked");
			exit(0);
		}
	}
}
?>
