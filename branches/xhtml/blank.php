<?php
/**
 * This gives a blank page.  Kept currently because some external tools
 * like a nice lightweight page to scrape stuff from.
 * @deprecated
 */
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$dbh = db_connect();
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
$thispage = new PlansPage('Utilities', 'blank', PLANSVNAME, 'blank.php');
if ($auth) {
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);
} else {
	get_guest_interface();
	populate_guest_page($thispage);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
