<?php
require_once ("../Plans.php");
$username = $_POST['username'];
require ("auth.php");
?>

<html>
<body>
<?php
require ("../dbfunctions.php");
$dbh = db_connect();
if ($mysubmit) {
	$plan = get_item($dbh, "plan", "accounts", "username", $username);
	$new_plan = substr($plan, 0, -1 * $length);
	set_item($dbh, "accounts", "plan", $new_plan, "username", $username);
}
?>
<form action="truncate.php" method="POST">
<br><input type="hidden" name="mysubmit" value="1">
<br><input type="submit" value="Truncate Plan">
<br>Username: <input type="text" name="username">
<br>How much to cut off the end:<input type="text" name="length">
</form>
<?php
db_disconnect($dbh);
?>
</html>
</body>
