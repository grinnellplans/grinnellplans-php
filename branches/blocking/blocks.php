<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'blocks', PLANSVNAME . ' - Blocking', 'blocks.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} else {
    populate_page($thispage, $dbh, $idcookie);

    if (isset($_REQUEST["unblock_user"])) {
        Block::removeBlock($idcookie, $_REQUEST["unblock_user"]);
        $success = new InfoText('User unblocked.');
        $thispage->append($success);
    }

    $header = new WidgetGroup('blocking_header', true);
    $heading = new HeadingText('Blocking', 1);
    $header->append($heading);
    $thispage->append($header);
    $about = new InfoText('Users that you have blocked will not be able to read your plan, and you will not see each other listed in quicklove or search results.
        <a href="/blocking-about.php">See the FAQ for more information</a>.
        <br /><br />
        <b>Please be aware that your activity on Notes is still visible to everyone.</b> If you feel this presents a serious problem to you, please <a href="mailto:grinnellplans@gmail.com">contact the administrators</a>.');
    $header->append($about);

    $heading = new HeadingText('Blocked Users', 2);
    $thispage->append($heading);
    $blocklist = new WidgetList('blocked_user_list', true);
    $thispage->append($blocklist);

    $q = Doctrine_Query::create()
        ->select("*")
        ->from("Accounts a")
        ->innerJoin("a.BlockedBy b")
        ->where("b.blocking_user_id = ?", $idcookie);
    $blocked_users = $q->execute();
    foreach ($blocked_users as $blocked_user) {
        $entry = new WidgetGroup('newplan', false);
        $plan = new PlanLink($blocked_user->username);

        $form = new Form('block', 'User blocking options');
        $item = new HiddenInput('unblock_user', $blocked_user->userid);
        $form->append($item);
        $item = new SubmitInput("Unblock $blocked_user->username");
        $form->append($item);

        $entry->append($plan);
        $entry->append($form);
        $blocklist->append($entry);
    }
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
