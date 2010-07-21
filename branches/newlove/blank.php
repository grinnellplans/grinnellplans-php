<?php
/**
 * This gives a blank page.  Kept currently because some external tools
 * like a nice lightweight page to scrape stuff from.
 * @deprecated
 */
require_once ('Plans.php');
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Utilities', 'blank', PLANSVNAME, 'blank.php');
if (User::logged_in()) {
    populate_page($thispage, $dbh, $idcookie);
} else {
    populate_guest_page($thispage);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
