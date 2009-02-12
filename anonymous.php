<?php
require_once('Plans.php');

require ("functions-main.php");
$dbh = db_connect();
$idcookie = User::id();

if (User::logged_in()) {
	$db = new Database();
	$db->query("delete from viewed_secrets where userid = $idcookie");
	$db->query("insert into viewed_secrets (userid, date) values($idcookie, now())");
}
?>

<?php display_header(); ?>

<p>Here you can post anonymously.  This page was created to help give wgemigh his plan back, and to make it possible to paginate an increasing number of secrets.  Please add this page as an optional link under 'preferences'.</p>
<p>Secrets cannot be tracked by any Plans administrator.  If you're still worried, you may log out before posting. We can exercise editorial discretion as to what shows up. <a href="#" onClick ="document.getElementById('secrets').style.display = 'block';">Post a Secret</a></p>
<div id="secrets">
	<form method="POST">
		<textarea name="secret" rows=10 cols=50></textarea>
		<input type="hidden" name="secret_submitted" value="1">
		<input type="submit" value="Post">
	</form>
</div>
<script>
<!-- 
    document.getElementById('secrets').style.display = 'none';
-->
</script>  
<?php
if (isset($_POST['secret_submitted'])) {
	$secret = $_POST['secret'];
	$secret = cleanText($secret);
	mysql_query("insert into secrets(secret_text, date, display) values (substring('$secret',1,4000), now(), 'no')");
}
?>
<?php
if (User::logged_in()) {
	$count = 100;
	$offset = (isset($_GET['offset']) ? $_GET['offset'] : 0);
	if (!is_numeric($offset)) {
		$offset = 0;
	}
	echo '<p><a href="anonymous.php?offset=' . ($offset + $count) . '">Older Secrets</a></p>';
	if (isset($_GET['show_all'])) {
		$select_query = "select * from secrets order by date desc limit $offset, $count";
	} else {
		$select_query = "select * from secrets where display = 'yes' or display = 'pref'  order by date desc limit $offset, $count";
	}
	if (!$secrets = mysql_query($select_query)) {
		echo "No secrets";
	} else {
		while ($row = mysql_fetch_array($secrets)) {
			echo '<p class="sub">';
			$secret = $row['secret_text'];
			$date = $row['date'];
			$secretidno = $row['secret_id'];
			echo "$secretidno <b>$date</b><br />\n";
			echo "$secret\n";
		}
		echo '</p>';
	}
}

display_footer();
?>