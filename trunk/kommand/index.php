<?
require_once("../cookie_session.php");
require("dbfunctions.php");
$dbh = db_connect();

/*
print_r($_POST);
echo "$username<br />\n";

echo $_POST['username'] . " is the PoSTed username<br/>\n";
*/
if ($_POST['username'] == 'plans') {

	$password = crypt($password, "ab"); 
	$read_pass = get_item($dbh, "password", "accounts", "username", "plans"); 
	//echo "You gave $password, and we need $read_pass <br />\n";

	if (($password == $read_pass) || $in)
	{
		$_SESSION['kommand_auth'] = 1;
		$_SESSION['kommand_logged_in'] = time();
	} else {
		$wrong_password = 1;
	}
}

echo "<br />\n";
if ($_SESSION['kommand_auth'] && (time() - $_SESSION['kommand_logged_in'] < 1800 )) {
?>
<html>
<body>
<a href="adduser.php">Add a User</a><br>
<a href="deleteuser.php">Delete User</a><br>
<a href="changepassword.php">Change Password</a><br>
<a href="changemotd.php">Change MOTD</a></br />
<a href="truncate.php">Truncate a Plan</a><br />
<a href="secrets.php">Manage Secrets</a><br />
<a href="manage-donations.php">Manage Donations</a><br />
<a href="/chat/usage.cgi">Chat Usage</a><br />
<a href="polls.php">Manage Polls</a><br />
<a href="new-accounts.cgi">New Account Usage </a><br />
<a href="update-frequency.cgi">Update Frequency</a><br />
<a href="swap-password.php">Switch a User's password with that of [test].</a><br />
<pre>
<?php
show_penetration();
?>
</pre>
</body>
</html>
<?

} else if ($wrong_passwrod) {

?>
<html><body>Wrong password.<br><form action="index.php" method="POST">
<input type="text" name="username"><Br>
<input type="password" name="password"><br>
<input type="submit" value="Kommand">
</form>
</body>
</html>
<?

} else {

?>
<html><body><form action="index.php" method="POST">
<input type="text" name="username"><Br>
<input type="password" name="password"><br>
<input type="submit" value="Kommand">
</form>
</body>
</html>
<?

}

db_disconnect($dbh);


function show_penetration() {
    system ('/title/grinnellplans.com/class-year-penetration/run-counts.sh');

    }
?>
