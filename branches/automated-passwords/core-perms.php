<?php
require_once('Plans.php');

require_once ("dbfunctions.php");
$dbh = db_connect();

$userid = User::id();

$status = get_item($dbh, "status", "perms", "userid", $userid);
$write_only_access = array('edit.php' => 1, 'index.php' => 1, 'quicklove.php' => 1, 'search.php' => 1, 'home.php' => 1);
if ($status && $status == 'write-only') {
	$php_self = basename($_SERVER['SCRIPT_FILENAME']);

	if ($write_only_access[$php_self] ||
		(($php_self == 'search.php') && (isset($_GET['mysearch'])) && ($_GET['mysearch'] == User::name())) ||
		(('read.php' === $php_self) && (isset($_GET['searchname'])) && ($_GET['searchname'] == User::name() && (isset($_GET['edit_submit'])) && ($_GET['edit_submit'] == 1)))) {
		// Okay
	} else {
		echo 'Sorry, ' . User::name() . ' is a "write-only" account, and as such does not have access to most Plans features out of respect for user privacy.';
		echo "<br />";
		echo "If it sounds like there's been some mistake, please email us at " . '<a href="mailto:grinnellplans@gmail.com">grinnellplans@gmail.com</a>.';
		exit(0);
	}
}
?>
