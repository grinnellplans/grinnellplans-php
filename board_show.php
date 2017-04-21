<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect(); //establish the database handler
$idcookie = User::id();
$thispage = new PlansPage('Notes', 'board_show', PLANSVNAME . ' - Notes', 'board_show.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to view notes.', 'Access Denied');
    $thispage->append($denied);
} else {
    populate_page($thispage, $dbh, $idcookie);
    $content = new WidgetGroup('notes_content', false);
    $thispage->append($content);
    $header = new WidgetGroup('notes_header', false);
    $content->append($header);
    $href = "board_submit.php?newthread=1";
    $newthread = new Hyperlink('notes_new_thread', true, $href, 'New Thread');
    $header->append($newthread);
    $my_result = mysqli_query($dbh,"Select COUNT(*) From mainboard");
    $totalthreads = mysqli_fetch_row($my_result);
    $max_page = ceil($totalthreads[0] / NOTES_THREADS_PER_PAGE) - 1;
    $nav = new NotesNavigation('board_nav', false);
    $header->append($nav);
    $pagenumber = (isset($_GET['pagenumber']) ? $_GET['pagenumber'] : 0);
    if (!($pagenumber > 0)) {
        $pagenumber = 0;
    }
    if ($pagenumber > $max_page) {
        $pagenumber = $max_page;
    }
    if ($pagenumber > 0) {
        $nav->newest = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=0', '&lt;&lt;');
    }
    if ($pagenumber >= 2) {
        $tempnum = $pagenumber - 2;
        $nav->even_newer = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=' . $tempnum, $tempnum);
    }
    if ($pagenumber >= 1) {
        $tempnum = $pagenumber - 1;
        $nav->newer = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=' . $tempnum, $tempnum);
    }
    $nav->current = new RegularText($pagenumber);
    if ($pagenumber <= $max_page - 1) {
        $tempnum = $pagenumber + 1;
        $nav->older = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=' . $tempnum, $tempnum);
    }
    if ($pagenumber <= $max_page - 2) {
        $tempnum = $pagenumber + 2;
        $nav->even_older = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=' . $tempnum, $tempnum);
    }
    if ($max_page > $pagenumber) {
        $nav->oldest = new Hyperlink('notes_nav_page', false, 'board_show.php?pagenumber=' . $max_page, '&gt;&gt;');
    }
    $rowoffset = NOTES_THREADS_PER_PAGE * $pagenumber;
    $board = new NotesBoard('notes_board', true);
    $content->append($board);
    $the_query = "Select
mainboard.threadid,
mainboard.title,
mainboard.lastupdated,
count(*),
accounts.username,
maxes.username
From
mainboard
left join (select username, tids.threadid from (select max(messageid) uid, subboard.threadid from subboard, accounts, (select threadid from mainboard order by lastupdated desc limit " . $rowoffset . "," . NOTES_THREADS_PER_PAGE . ") these where subboard.userid = accounts.userid and subboard.threadid = these.threadid group by subboard.threadid) tids, subboard, accounts where tids.uid = subboard.messageid and subboard.userid = accounts.userid) maxes
using
(threadid)
left join
subboard
using
(threadid)
left join
accounts
on mainboard.userid =
accounts.userid
GROUP
BY
threadid
ORDER
BY
lastupdated
DESC
LIMIT " . $rowoffset . "," . NOTES_THREADS_PER_PAGE;
    $my_result = mysqli_query($dbh,$the_query);
    //error_log($the_query);
    $colorswitch = 0;
    while ($new_row = mysqli_fetch_row($my_result)) {
        $topic = new NotesTopic('notes_topic', false);
        $topic->summary = true;
        $board->append($topic);
        if ($new_row[4]) {
            $topic->firstposter = new PlanLink($new_row[4]);
        }
        if ($new_row[5]) {
            $topic->lastposter = new PlanLink($new_row[5]);
        }
        $topic->title = new Hyperlink('topic_link', false, 'board_messages.php?threadid=' . $new_row[0], stripslashes($new_row[1]));
        $topic->updated = strtotime($new_row[2]);
        $topic->posts = $new_row[3];
    }
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
