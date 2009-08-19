<?php
require_once('Plans.php');
require('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect(); ///connect to the database
$idcookie = User::id();

// Create the new page
$page = new PlansPage('Main', 'edit', PLANSVNAME . ' - Edit Plan', 'edit.php');

if (!User::logged_in()) {
	populate_guest_page($page);
	//tell them not able to use page
	$page->append(new AlertText("You are not allowed to edit as a guest.", false));
} else {
	populate_page($page, $dbh, $idcookie);

	// Get the user and plan information
	$q = Doctrine_Query::create()
		->from('Accounts a')
		->leftJoin('a.Plan p')
		->where('a.userid = ?', $idcookie);
	$user = $q->fetchOne();

	if (!isset($_POST["plan"])) {
		// If this is the test server, give them a warning
		if ($GLOBALS['ENVIRONMENT'] == 'testing') {
			$betawarn = new AlertText('Remember, any changes you make here won\'t show up on regular Plans.', 'We\'re in beta');
			$page->append($betawarn);
		}

		// Add the edit form
		$plantext = $user->Plan->edit_text;
		$plan = new PlanText($plantext, true);
		$editbox = new EditBox($user->username, $plan, $user->edit_rows, $user->edit_cols);
		$editbox->action = 'edit.php';
		$editbox->method = 'post';
		$page->append($editbox);

	} else {

		// Rename received text
		$plan = $_POST["plan"];

		// Get the pre-edit text.
		$old_plan = $user->Plan->edit_text;

		// Take a diff versus the new version and store it.
		$diff = xdiff_string_diff($old_plan, $plan);
		mysql_query("insert into diffs(userid, text, date) values($idcookie, \"$diff\", now())");

		// Store the edited plan source, convert it, and store the converted text.
		$user->Plan->edit_text = $plan;
		$user->Plan->save();
		setUpdatedTime($idcookie); //set the time which keeps track of when the plan was last updated
		set_item($dbh, "autofinger", "updated", 1, "interest", $idcookie); //make the plan show up as updated on other people's autoread list.

		Redirect('read.php?edit_submit=1&searchname=' . User::name());
	}

} //allow to edit if user
/* display the page */
interface_disp_page($page);
db_disconnect($dbh);
?>

