<?php
session_start();
$subject_name = $_POST['username'];
require("auth.php");
?>

<html>
<body>
<?

require("dbfunctions.php");
$dbh = db_connect();

if ($subject_name)
{


if(isvaliduser($dbh, $subject_name))
{
$info = get_items($dbh,"userid,email","accounts","username", $subject_name);
$subject_id= $info[0][0];
$email = $info[0][1];

if(!$password)
{
srand(time());
$password = rand(0,999999);
}
$crpassword = crypt($password, "ab");

set_item($dbh, "accounts", "password", $crpassword, "userid", $subject_id);

echo "<form action=\"email.php\" method=\"POST\">";
echo "<input type=\"hidden\" name=\"email\" value=\"" . $email . "\">";
echo "<input type=\"hidden\" name=\"username\" value=\"" . $subject_name . 
"\">";
echo "<input type=\"hidden\" name=\"password\" value=\"" . $password . 
"\">";
echo "<input type=\"hidden\" name=\"whatoperation\" 
value=\"changepassword\">";
echo "<input type=\"submit\" value=\"Send Email\"></form>";

}
else
{
echo $subject_name . "does not exist.";
}


}//if a username
{//if no username
?>
<form action="changepassword.php" method="POST">
Username : <input type="text" name="username"><br>
Password : <input type="text" name="password"><br>
<input type="submit" value="Change Password">
</form>
<?


}


db_disconnect($dbh);
?>
</html>
</body>
