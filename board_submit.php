<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect(); //establish the database handler
$idcookie = User::id();
$thispage = new PlansPage('Notes', 'board_submit', PLANSVNAME . ' - Notes', 'board_submit.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to use Notes as a guest.', 'Access Denied');
    $thispage->append($denied);
}
//TODO why is this here?
else if (User::id() == 0) {
    User::logout();
} else
//elseallowed to edit
{
    populate_page($thispage, $dbh, $idcookie);
    $showform = 1;
    $newthread = (isset($_REQUEST['newthread']) && ($_REQUEST['newthread'] === "1"));
    $threadid = (isset($_REQUEST['threadid'])?(int)$_REQUEST['threadid']:false);
    $threadtitle = isset($_POST['threadtitle'])?$_POST['threadtitle']:"";
    $messagetitle = isset($_POST['messagetitle'])?$_POST['messagetitle']:"";
    $messagecontents = isset($_POST['messagecontents'])?$_POST['messagecontents']:"";
    $error_message = '';
    if (isset($_POST['submit'])) {
        $showform = 0;
        if ($newthread) {
            $threadtitle = cleanText($threadtitle);
            $threadtitle = preg_replace('/<[^>]*>/', '', $threadtitle);
            if (!$threadtitle || preg_match("/JLW/", $threadtitle)) {
                $showform = 1;
                $error_message = new AlertText("You are creating a new thread. Please enter a title for the thread.", 'Error');
            } //if no threadtitle
            
        } //if newthread
        else {
            if (!$threadid || !(get_item($dbh, "threadid", "mainboard", "threadid", $threadid))) {
                $showform = 1;
                $error_message = new AlertText("Invalid parent thread.", 'Error');
            }
        } //if not new thread
        $messagetitle = cleanText($messagetitle);
        $messagecontents = cleanText($messagecontents);
        if (!$messagecontents) {
            $showform = 1;
            $error_message = new AlertText("Please enter a message.", 'Error');
        }
        if (!$showform) {
            if ($newthread) {
                mysqli_query($dbh,"INSERT INTO mainboard VALUES(\"\",\"" . mysqli_real_escape_string($dbh,$threadtitle) . "\",NOW(),NOW(),\"" . $idcookie . "\")");
                $threadid = mysqli_insert_id($dbh);
            }
            mysqli_query($dbh,"INSERT INTO subboard VALUES(\"\",\"" . $threadid . "\",NOW(),\"" . $idcookie . "\", \"" . mysqli_real_escape_string($dbh,$messagetitle) . "\", \"" . mysqli_real_escape_string($dbh,$messagecontents) . "\")");
            mysqli_query($dbh,"UPDATE mainboard SET lastupdated = NOW() WHERE threadid = \"" . $threadid . "\"");
            //			echo "Your message has been submitted.";
            //process message here
            
        }
    } //if submit
    if ($error_message instanceof Widget) {
        $thispage->append($error_message);
    } else if (!$showform) {
        header('Location: board_messages.php?threadid=' . $threadid);
    }
    if ($showform) {
        $messageform = new Form('notesform', true);
        $thispage->append($messageform);
        if ($newthread) {
            $title = new TextInput('threadtitle', $threadtitle);
            $title->title = 'Thread Title:';
            $hid = new HiddenInput('newthread', 1);
            $messageform->append($title);
            $messageform->append($hid);
        } //if ($newthread)
        $message = new TextInput('messagetitle', $messagetitle);
        $message->title = 'Message Title:';
        $messageform->append($message);
        $messagecontents = new TextareaInput('messagecontents', $messagecontents);
        $messagecontents->title = 'Message Contents:';
        $messagecontents->rows = 11;
        $messagecontents->cols = 50;
        $messageform->append($messagecontents);
        $threadid = new HiddenInput('threadid', $threadid);
        $messageform->append($threadid);
        $hidsubmit = new HiddenInput('submit', 1);
        $messageform->append($hidsubmit);
        $button = new SubmitInput('Submit Message');
        $messageform->append($button);
    } //if showform
    
} //if valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>
