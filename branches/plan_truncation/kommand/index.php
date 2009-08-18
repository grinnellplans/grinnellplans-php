<?php
require_once ("../Plans.php");
require ("dbfunctions.php");
if (User::is_admin()) {
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
<a href="style-stats.php">Display Prefs Statistics</a><br/>
<pre>
<?php
	show_penetration();
?>
</pre>
</body>
</html>
<?php
} else if ($wrong_passwrod) {
?>
<html><body>Wrong password.<br><form action="index.php" method="POST">
<input type="text" name="username"><Br>
<input type="password" name="password"><br>
<input type="submit" value="Kommand">
</form>
</body>
</html>
<?php
} else {
?>
<html><body><form action="index.php" method="POST">
<input type="text" name="username"><Br>
<input type="password" name="password"><br>
<input type="submit" value="Kommand">
</form>
</body>
</html>
<?php
}
function show_penetration()
{
//	system('/title/grinnellplans.com/class-year-penetration/run-counts.sh');
}
?>
