<?php
require_once('Plans.php');
require('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Utilities', 'plangenesis', PLANSVNAME . ' - New Plans', 'lastcreated.php');

if (!User::logged_in()) {
	get_guest_interface();
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to see this as a guest.', 'Access Denied');
	$thispage->append($denied);
}
else {
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);

	$heading = new HeadingText('Plans created within the last 5 days', 2);
	$thispage->append($heading);

	$my_result = mysql_query("Select userid,username,DATE_FORMAT(created,'%l:%i %p, %a %M %D ') From accounts where created > DATE_SUB(NOW(), 
		INTERVAL 5
		DAY) ORDER BY created desc");

	$newplanslist = new WidgetList('new_plan_list', true);
	$thispage->append($newplanslist);

	while ($new_row = mysql_fetch_row($my_result)) {
		$entry = new WidgetGroup('newplan', false);
		$plan = new PlanLink($new_row[1]);
		$time = new RegularText($new_row[2], 'Date Created');
		$entry->append($plan);
		$entry->append($time);
		$newplanslist->append($entry);
	}
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
