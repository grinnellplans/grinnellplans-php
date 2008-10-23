<?php
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load main functions
$dbh = db_connect();
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
// initialize page classes
$thispage = new PlansPage('Preferences', 'webview', PLANSVNAME . ' - Guest Viewable', 'webview.php');
// If user is not authorized, turn them away.
if (!$auth) {
	get_guest_interface();
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
	$thispage->append($denied);
} else {
	//allowed to edit
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);
	$heading = new HeadingText('Guest Viewable', 1);
	$thispage->append($heading);
	// If the form was submitted, set the preference and print a message
	if (($webview = $_POST['webview']) != '') {
		if ($webview != 1) {
			$webview = 0;
		}
		set_item($dbh, "accounts", "webview", $webview, "userid", $idcookie);
		$thisitem = new InfoText('Preference set.', '');
		$thispage->append($thisitem);
	}
	// Make our form
	$viewableform = new Form('guestviewableform', true);
	$thispage->append($viewableform);
	$viewableform->action = 'webview.php';
	$viewableform->method = 'POST';
	if (get_item($dbh, "webview", "accounts", "userid", $idcookie) == 1) {
		$viewable = true;
	} else {
		$viewable = false;
	}
	$item = new FormItem('hidden', 'part', 1);
	$viewableform->append($item);
	$item = new FormItem('radio', 'webview', 1);
	$item->checked = $viewable;
	$item->description = "Make plan viewable to guests.";
	$viewableform->append($item);
	$item = new FormItem('radio', 'webview', 0);
	$item->checked = !($viewable);
	$item->description = "Make plan unviewable to guests.";
	$viewableform->append($item);
	$item = new FormItem('submit', NULL, 'Change');
	$viewableform->append($item);
} //if is a valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>

