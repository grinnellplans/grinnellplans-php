<?php
require_once ('Plans.php');
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display classes
$dbh = db_connect(); //connect to database
$idcookie = User::id();
// Create the new page
$page = new PlansPage('Extras', 'secrets', PLANSVNAME . ' - Secrets', 'anonymous.php');
if (User::logged_in()) {
    $db = new Database();
    $db->query("insert into viewed_secrets (userid, date) values($idcookie, now()) on duplicate key update date=now()");
    populate_page($page, $dbh, $idcookie);
} else
//begin guest user display
{
    populate_guest_page($page);
}
$infotext = "Here you can post anonymously.  This page was created to help give wgemigh";
$infotext.= " his plan back, and to make it possible to paginate an increasing number of";
$infotext.= " secrets.  Please add this page as an optional link under 'preferences'.";
$page->append(new InfoText($infotext));
$infotext2.= "Secrets cannot be tracked by any Plans administrator.  If you're still worried,";
$infotext2.= " you may log out before posting. We can exercise editorial discretion as to what shows up.";
$page->append(new InfoText($infotext2));
$submitform = new Form('submitsecret', true);
$hidden = new HiddenInput('secret_submitted', 1);
$submitform->append($hidden);
$text = new TextareaInput('secret', null);
$text->rows = 10;
$text->cols = 50;
$submitform->append($text);
$submit = new SubmitInput('Post');
$submitform->append($submit);
$post_secret = new DisplayToggleLink('post_secret_toggle', true, $submitform, 'Post a Secret', 'Cancel');
$page->append($post_secret);
$page->append($submitform);
if (isset($_POST['secret_submitted'])) {
    $secret = $_POST['secret'];
    $secret = cleanText($secret);
    $secret = mysql_real_escape_string($secret);
    $sql = "insert into secrets(secret_text, date, display) values (substring('$secret',1,4000), now(), 'no')";
    mysql_query($sql);
}
if (User::logged_in()) {
    $count = 100;
    $offset = (isset($_GET['offset']) ? $_GET['offset'] : 0);
    if (!is_numeric($offset)) {
        $offset = 0;
    }
    $linkbox = new WidgetGroup("linkbox",false);
    $linkbox->append(new RegularText("<br />"));
    if (isset($offset) && $offset > 0) {
       $link = new Hyperlink('newer_secrets', false, 'anonymous.php?offset=' . ($offset - $count), 'Newer Secrets');
       $linkbox->append($link);
    }
    $link = new Hyperlink('older_secrets', false, 'anonymous.php?offset=' . ($offset + $count), 'Older Secrets');
    $linkbox->append($link);
    $select_query = "select * from secrets where display = 'yes' or display = 'pref'  order by date desc limit $offset, $count";
    if (!$secrets = mysql_query($select_query)) {
        $page->append(new AlertText("No secrets", false));
    } else {
        $box_o_secrets = new WidgetGroup('secrets', true);
	$box_o_secrets->append($linkbox);
        $page->append($box_o_secrets);
        while ($row = mysql_fetch_array($secrets)) {
            $text = $row['secret_text'];
            $secret = new Secret($text);
            $secret->date = strtotime($row['date']);
            $secret->secret_id = $row['secret_id'];
            $box_o_secrets->append($secret);
        }
        $box_o_secrets->append($linkbox);
    }
}
interface_disp_page($page);
db_disconnect($dbh);
?>
