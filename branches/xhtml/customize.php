<?php
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load main functions
$dbh = db_connect(); //connect to database
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
// initialize page classes
$thispage = new PlansPage('Preferences', 'prefs', PLANSVNAME . ' - Preferences', 'customize.php');
if (!$auth) {
	get_guest_interface();
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
	$thispage->append($denied);
} else {
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);
	$heading = new HeadingText('Preferences', 1);
	$thispage->append($heading);
	$preflist = new WidgetList('preflist', "Preferences");
	// make the list of preference pages
	$arr = array('Change Auto List' => "autoread.php", 'Change Password' => "changepassword.php", 'Change Name' => "changename.php", 'Guest Readable' => "webview.php", 'Customize' => '', 'Interfaces' => "interfaces.php", 'Styles' => "styles.php", 'Edit Text Box Size' => "textbox.php", 'Optional Links' => "links.php");
	foreach($arr as $name => $ref) {
		if (strtolower($name) != 'customize') {
			$alink = new Hyperlink(null, $ref, $name);
			$preflist->append($alink);
		} else {
			$aheading = new HeadingText('Customize', 2);
			$preflist->append($aheading);
		}
	}
	$thispage->append($preflist);
} //if is a valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>
