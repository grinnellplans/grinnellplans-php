<?php
session_start();
$username = $_POST['username'];
require("auth.php");
?>

<html>
<body>
<?

require("dbfunctions.php");
$dbh = db_connect();


if ($username)
{

$usersid = get_item($dbh,"userid","accounts","username", $username);
if ($mysubmit)
{

$newmessage = preg_replace("/\n/s","<br>", $newmessage);
$newmessage = addslashes($newmessage);

set_item($dbh, "accounts", "spec_message", $newmessage,
"userid", $usersid);

echo "Private message for <b>" . $username. "</b> set to:<br><br>" . 
stripslashes(stripslashes($newmessage));


}//if submitted
else
{//not submitted, so show spec_message
$spec_message = get_item($dbh,"spec_message","accounts","userid", $usersid);
?>
<form action="changespec.php" method="POST">
<textarea name="newmessage" cols="70" rows="14" wrap="physical">
<?=$spec_message?>
</textarea><br>
<input type="hidden" name="mysubmit" value="1">
<input type="hidden" name="username" value="<?=$username?>">
<input type="submit" value="Change Private Message">
</form>
<?

}//end show spec_message

}//if username
else
{
if ($username) {echo "Invalid Username.<br>";}

?>
<form action="changespec.php" method="POST">
Username: <input type="text" name="username" value="<?=$username?>"><br>
<input type="submit" value="Get Private Message">
</form>
<?
}//if no username

db_disconnect($dbh);
?>
</html>
</body>


