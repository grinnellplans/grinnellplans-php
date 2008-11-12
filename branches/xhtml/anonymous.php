<?php
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load main functions
$dbh = db_connect(); //connect to database
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];

// Create the new page
$page = new PlansPage('Extras', 'secrets', PLANSVNAME . ' - Secrets', 'anonymous.php');

if ($auth) //begin valid user display
{
	mysql_query("delete from viewed_secrets where userid = $idcookie");
	mysql_query("insert into viewed_secrets (userid, date) values($idcookie, now())");
	get_interface($idcookie);
	populate_page($page, $dbh, $idcookie);
} else
//begin guest user display
{
	get_guest_interface();
	populate_guest_page($page);
}

$infotext = "Here you can post anonymously.  This page was created to help give wgemigh";
$infotext .= " his plan back, and to make it possible to paginate an increasing number of";
$infotext .= " secrets.  Please add this page as an optional link under 'preferences'.";
$page->append(new InfoText($infotext));

$infotext2 .= "Secrets cannot be tracked by any Plans administrator.  If you're still worried,";
$infotext2 .= " you may log out before posting. We can exercise editorial discretion as to what shows up.";
$page->append(new InfoText($infotext2));

$submitform = new Form('submitsecret', true);
$hidden = new FormItem('hidden', 'secret_submitted', 1);
$submitform->append($hidden);
$text = new FormItem('textarea', 'secret', null);
$text->rows = 10;
$text->cols = 50;
$submitform->append($text);
$submit = new FormItem('submit', null, 'Post');
$submitform->append($submit);

$page->append($submitform);

if ($_POST['secret_submitted']) {
	$secret = $_POST['secret'];
	$secret = cleanText($secret);
	$sql = "insert into secrets(secret_text, date, display) values (substring('$secret',1,4000), now(), 'no')";
	mysql_query($sql);
}
if ($auth) {
	$count = 300;
	$offset = $_GET['offset'];
	if (!is_numeric($offset)) {
		$offset = 0;
	}
	echo "<!--- $offset --->";
	if ($_SERVER['REMOTE_ADDR'] == '70.12.157.227' || $_GET['show_all']) {
		$select_query = "select * from secrets order by date desc limit $offset, $count";
	} else {
		$select_query = "select * from secrets where display = 'yes' or display = 'pref'  order by date desc limit $offset, $count";
	}
	if (!$secrets = mysql_query($select_query)) {
		$page->append(new AlertText("No secrets", false));
	} else {
		$box_o_secrets = new WidgetGroup('secrets', true);
		$page->append($box_o_secrets);
		while ($row = mysql_fetch_array($secrets)) {
			$text = $row['secret_text'];
			$secret = new Secret($text);
			$secret->date = $row['date'];
			$secret->secret_id = $row['secret_id'];
			$box_o_secrets->append($secret);
		}
	}
}
interface_disp_page($page);
db_disconnect($dbh);
?>
