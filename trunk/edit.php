<?php
require_once ('Plans.php');
require ('functions-main.php');
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
    $q = Doctrine_Query::create()->from('Accounts a')->innerJoin('a.Plan p')->where('a.userid = ?', $idcookie)->orderBy('p.id ASC');
    $user = $q->fetchOne();
    if (!isset($_POST["plan"])) {
        // If this is the test server, give them a warning
        if ($GLOBALS['ENVIRONMENT'] == 'testing') {
            $betawarn = new AlertText('Remember, any changes you make here won\'t show up on regular Plans.', 'We\'re in beta');
            $page->append($betawarn);
        }
        // Add the edit form
        $plantext = $user->Plan->edit_text;
	$editbox = make_editbox($plantext, $user, 1);       
 	$page->append($editbox);
        $editbox->append(new HiddenInput('edit_text_md5', md5($plantext)));
	$log_msg = "Edit page loaded: id $idcookie, edit_text \"" . substr($plantext, 0, 50) . "..." . substr($plantext, -50) . '"';
        trigger_error($log_msg, E_USER_NOTICE);
    } else {
        // Rename received text
        $pre_edit_md5 = $_POST["edit_text_md5"];
        $plan = $_POST["plan"];

        $log_msg = "Plan updated: id $idcookie, post \"" . substr($plan, 0, 50) . "..." . substr($plan, -50) . '"';
        trigger_error($log_msg, E_USER_NOTICE);

        // Get the pre-edit text.
        $old_plan = $user->Plan->edit_text;
	
	unset($_SESSION['d']); // clean up from april fools 2012

	if (md5($old_plan) != $pre_edit_md5) {
		$page->append(new AlertText("Your plan was edited from another instance of the edit page, or there was a corruption while transferring data between the server and you. Here's a copy of what you submitted:", "Your plan may have changed after you loaded this Edit page!"));
		$page->append(make_editbox($plan, $user, 0));
		interface_disp_page($page);
		db_disconnect($dbh);
		exit;
	}
        // Store the edited plan source, convert it, and store the converted text.
        try {
            $user->Plan->edit_text = $plan;
            $user->Plan->save();

            $user->changed = new Doctrine_Expression('NOW()'); //set the time which keeps track of when the plan was last updated
            $user->save();
            Autofinger::updateFor($idcookie);
            // Leave this page!
            Redirect('read.php?edit_submit=1&searchname=' . User::name());
            exit;
        }
        catch(Doctrine_Validator_Exception $e) {
            $errmsg = 'Sorry, your plan is too long. Please remove some text and try again.';
            $err = new AlertText($errmsg, 'Maximum Plan length exceeded');
            $page->append($err);
            $page->append(make_editbox($plan, $user));
        }
    }
} //allow to edit if user
/* display the page */
interface_disp_page($page);
db_disconnect($dbh);
function make_editbox($plantext, $user, $edit_button = 1) {
    $plan = new PlanText($plantext, true);
    $editbox = new EditBox($user->username, $plan, $user->edit_rows, $user->edit_cols, $edit_button);
    $editbox->action = 'edit.php';
    $editbox->method = 'post';
    return $editbox;
}
?>

