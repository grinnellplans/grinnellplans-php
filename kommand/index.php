<?php
require_once ("../Plans.php");
?>
<html>
<body>
<?php
require ("dbfunctions.php");
if (User::is_admin()) {
?>
<a href="adduser.php">Add a User</a><br>
<a href="deleteuser.php">Delete User</a><br>
<a href="changeemail.php">Change Email</a><br>
<a href="changepassword.php">Change Password</a><br>
<a href="changewriteonly.php">Change Write-only</a><br>
<a href="changemotd.php">Change MOTD</a><br />
<a href="secrets.php">Manage Secrets</a><br />
<a href="manage-donations.php">Manage Donations</a><br />
<a href="/chat/usage.cgi">Chat Usage</a><br />
<a href="polls.php">Manage Polls</a><br />
<a href="new-accounts.cgi">New Account Usage </a><br />
<a href="update-frequency.cgi">Update Frequency</a><br />
<a href="swap-password.php">Switch a User's password with that of [test].</a><br />
<a href="style-stats.php">Display Prefs Statistics</a><br/>
<?php
} else {
?>
Usage of these tools is restricted to administrators.
<?php
}
?>
</body>
</html>
