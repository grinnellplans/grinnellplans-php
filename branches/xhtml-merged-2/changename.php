<?php
require_once('Plans.php');
require('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect();
$idcookie = User::id();
// Create the new page
$page = new PlansPage('Preferences', 'planname', PLANSVNAME . ' - Change Name', 'changename.php');
if (!User::logged_in()) {
	populate_guest_page($page);
	//tell them not able to use page
	$page->append(new AlertText("You are not allowed to edit this option as a guest.", false));
	interface_disp_page($page);
} else
//allowed to edit
{
	populate_page($page, $dbh, $idcookie);
	$title = new HeadingText('Change Name', 1);
	$page->append($title);
	if ($changed == 1) //check to see if form has been submitted, process if so.
	{
		$user_name = htmlspecialchars($user_name); //strip out html chars
		set_item($dbh, "accounts", "pseudo", $user_name, "userid", $idcookie); //set pseudoname in database
		$message = new InfoText("Name changed to <b>" . stripslashes($user_name) . "</b>.", NULL); //tell user their name has been changed
		$page->append($message);
	} //if changing name
	else { //if not changing name, give form
		$old_name = stripslashes(get_item($dbh, "pseudo", "accounts", "userid", $idcookie)); //get old name
		/* create the page and form */
		$nameform = new Form('changename', 'Change Name');
		$page->append($nameform);
		$nameform->action = 'changename.php';
		$nameform->method = 'POST';
		/* add fields to the form */
		$item = new TextInput('user_name', $old_name);
		$nameform->append($item);
		$item = new HiddenInput('myprivl', $myprivl);
		$nameform->append($item);
		$item = new HiddenInput('changed', 1);
		$nameform->append($item);
		$item = new SubmitInput('Change Name');
		$nameform->append($item);
	} //else, if not changing name, give form
	interface_disp_page($page);
} //if is a valid user
db_disconnect($dbh);
?>
