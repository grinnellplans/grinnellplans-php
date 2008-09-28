<?php
require_once ("Plans.php");
require ("syntax-classes.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect(); ///connect to the database
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
// Create the new page
$page = new PlansPage('Main', 'edit', PLANSVNAME . ' - Edit Plan', 'edit.php'); //TODO
if (!$auth) {
	get_guest_interface();
	populate_guest_page($page);
	//tell them not able to use page
	$page->append(new AlertText("You are not allowed to edit as a guest.", false));
} else
//allowed to edit
{
	get_interface($idcookie);
	populate_page($page, $dbh, $idcookie);
	if (!$part) //if nothing submitted yet
	{
		$myedit = get_items($dbh, "plan,edit_cols,edit_rows,username", "accounts", "userid", $idcookie); //get plan as well as what the size of the plan should be and a username
		$plantext = $myedit[0][0]; //put the contents of the plan into the plantext variable for easier use.
		//Make sure that the size of the edit plan is above 0
		if ($myedit[0][1] < 1) {
			$myedit[0][1] = 70;
		}
		if ($myedit[0][2] < 1) {
			$myedit[0][2] = 14;
		}
		//TODO needed?
		//change <br>'s into \n's
		$plantext = preg_replace("/<br>/", "", $plantext);
		$plantext = preg_replace("/&amp;/s", "&", $plantext);
		$plantext = preg_replace("/amp;/s", "", $plantext);
		$plantext = preg_replace("/<hr><p.class=\"sub\">/", "<hr>", $plantext);
		//change plan links into regular form
		$plantext = preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"planlove\"\>(.*?)\<\/a\>\]/s", "[\\1]", $plantext);
		$plantext = preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"boardlink\"\>(.*?)\<\/a\>\]/s", "[\\1]", $plantext);
		//change plan links into regular form
		$plantext = preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"boardlink\"\>#(.*?)\<\/a\>\]/s", "[\\1]", $plantext);
		//strip out CSS info from regular links on plans
		$plantext = preg_replace("/\<a\shref=\"([^\"]*?)\"\sclass=\"onplan\"\>(.*?)\<\/a\>/s", "[\\1\|\\2]", $plantext);
		//strip slashes that were added to plan to make sure the database didn't have any troubles with it
		$plantext = stripslashes($plantext);
		/* Add the edit form */
		$plan = new PlanText($plantext, true);
		$editbox = new EditBox($myedit[0][3], $plan, $myedit[0][2], $myedit[0][1]);
		$editbox->action = 'edit.php';
		$editbox->method = 'post';
		$page->append($editbox);
	} //if (!$part)
	else
	//if form info submitted, process
	{
		//TODO We got some diffing going on, but also a lot of cruft.
		// Kill whatever isn't needed.
		$diff_data = get_items($dbh, "edit_text", "accounts", "userid", $idcookie); //get plan as well as what the size of the plan should be
		$old_plan = $diff_data[0][0]; //put the contents of the plan into the plan variable for easier use.
		$diff = xdiff_string_diff($old_plan, $plan);
		$diff = $diff;
		mysql_query("insert into diffs(userid, text, date) values($idcookie, \"$diff\", now())");
		//add_row($dbh, 'diffs', array($idcookie, $diff, 'now()'));
		set_item($dbh, "accounts", "edit_text", $plan, "userid", $idcookie);
		$plan = cleanText($plan);
		set_item($dbh, "accounts", "plan", $plan, "userid", $idcookie); //set plan in database
		setUpdatedTime($idcookie); //set the time which keeps track of when the plan was last updated
		set_item($dbh, "autofinger", "updated", 1, "interest", $idcookie); //make the plan show up as updated on other people's autoread list.
		$plan = stripslashes($plan); //strip the slashes for display
		$page->append(new InfoText($plan, "Plan Changed To:"));
	} //if (!$part) else
	
} //allow to edit if user
/* display the page */
interface_disp_page($page);
db_disconnect($dbh);
?>
