<?php
require_once ("../Plans.php");
require ("auth.php");
?>

<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if (isset($_POST['username'])) {
    $user = User::get($_POST['username']);
    if ($user) {
        User::changePassword($user->username,$_POST['password'],null,true);
        echo "<form action=\"email.php\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"email\" value=\"" . $user->email . "\">";
        echo "<input type=\"hidden\" name=\"username\" value=\"" . $user->username . "\">";
        echo "<input type=\"hidden\" name=\"password\" value=\"" . $_POST['password'] . "\">";
        echo "<input type=\"hidden\" name=\"whatoperation\" 
value=\"changepassword\">";
        echo "<input type=\"submit\" value=\"Send Email\"></form>";
    } else {
        echo $_POST['username'] . "does not exist.";
    }
} //if a username
{ //if no username
    
?>
<form action="changepassword.php" method="POST">
Username : <input type="text" name="username"><br>
Password : <input type="text" name="password"><br>
<input type="submit" value="Change Password">
</form>
<?php
}
db_disconnect($dbh);
?>
</html>
</body>
