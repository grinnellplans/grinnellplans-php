<?php
require_once ('Plans.php');
?><html><head><title>Reset Style Sheet</title></head><body><center>

<?php
require ("functions-main.php");
if (isset($_POST['username'])) {
    $dbh = db_connect();
    if (User::checkPassword($_POST['username'], $_POST['password'])) {
        $idcookie = get_item($dbh, "userid", "accounts", "username", $_POST['username']);
        set_item($dbh, "display", "style", "1", "userid", $idcookie);
        delete_item($dbh, "stylesheet", "userid", $idcookie);
        echo "Your style sheet has been reset.";
    } else {
        echo "Invalid username or password";
    }
    db_disconnect($dbh);
} else {
?>
<a href="index.php">Back</a><br>
Reset style sheet for your account.
<form action="reset.php" method="post">
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit" value="Reset">
</form>

<?php
}
?>
</center>
</body>
</html>
