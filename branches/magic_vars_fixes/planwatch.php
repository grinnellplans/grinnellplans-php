<?php
require_once ('Plans.php');
require ("functions-main.php");
require ("syntax-classes.php");
$idcookie = User::id();
$thispage = new PlansPage('Utilities', 'planwatch', PLANSVNAME . ' - Recently Changed Plans', 'planwatch.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
} else {
    populate_page($thispage, $dbh, $idcookie);
}
$mytime = $_POST['mytime'];
$mytime = (isset($_POST['mytime']) ? $_POST['mytime'] : 12);
if (!($mytime > 0 and $mytime < 100)) {
    $mytime = 12;
} //if time is out of acceptable period, set to 12
//give form to set how many hours back to look
$timeform = new Form('planwatchtimeform', true);
$timeform->action = 'planwatch.php';
$timeform->method = 'POST';
$thispage->append($timeform);
$item = new TextInput('mytime', $mytime);
$item->title = 'Plans updated in the past:';
$item->description = 'hours';
$item->cols = 2;
$timeform->append($item);
$item = new SubmitInput('See Plans');
$timeform->append($item);
$q = Doctrine_Query::create()
    ->select("userid,username,DATE_FORMAT(changed,
'%l:%i %p, %a %M %D ') as updated")
    ->from("Accounts a")
    ->where("changed > DATE_SUB(NOW(), INTERVAL ? HOUR)", $mytime)
    ->orderBy("changed desc");
if (User::logged_in()) {
    $q->andWhereNotIn("userid",
        Block::allUserIdsWithBlockingRelationships(User::id()));
} else {
    $q->andWhere('webview = 1');
}
$results = $q->fetchArray();
//do the query with specifying date format to be returned
$newplanslist = new WidgetList('new_plan_list', true);
$thispage->append($newplanslist);
//display the results of the query
foreach ($results as $new_plans) {
    $entry = new WidgetGroup('newplan', false);
    $plan = new PlanLink($new_plans["username"]);
    $time = new RegularText($new_plans["updated"], 'Date Created');
    $entry->append($plan);
    $entry->append($time);
    $newplanslist->append($entry);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?> 
