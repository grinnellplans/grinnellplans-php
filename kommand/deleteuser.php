<?php
require_once ("../Plans.php");
$deleted_name = $_POST['username'];
require ("auth.php");
?>

<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if ($deleted_name) {
	if (isvaliduser($dbh, $deleted_name)) {
		$deleted_id = get_item($dbh, "userid", "accounts", "username", $deleted_name);
		delete_item($dbh, "accounts", "userid", $deleted_id);
		delete_item($dbh, "autofinger", "owner", $deleted_id);
		delete_item($dbh, "autofinger", "interest", $deleted_id);
		delete_item($dbh, "display", "userid", $deleted_id);
		delete_item($dbh, "opt_links", "userid", $deleted_id);
		echo "Account deleted";
	} else {
		echo $deleted_name . " does not exist.";
	}
} //if a username
{ //if no username
	
?>
<form action="deleteuser.php" method="POST">
Username : <input type="text" name="username"><br>
<input type="submit" value="Delete User">
</form>
<?php
}
db_disconnect($dbh);
?>
</html>
</body>
