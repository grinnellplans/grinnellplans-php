<?php
require_once ("../Plans.php");
require ("auth.php");
?>
<html>
<head>
    <title>Change Message of the Day</title>
</head>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if (isset($_POST['submit'])) {
    $motd = addslashes(preg_replace("/\n/s", "<br>", $_POST['motd']));
    mysql_query(sprintf("UPDATE `system` SET motd = '%s'", mysql_real_escape_string($motd)));
}
$row = mysql_fetch_array(mysql_query("SELECT motd FROM system"));
$motd = preg_replace("/<br>/", "\n", stripslashes($row[0]));
?>
<form action="changemotd.php" method="POST">
<textarea name="motd" cols="100" rows="40" >
<?php echo $motd ?>
</textarea>
<input type="submit" name="submit" value="Change MOTD">
</form>
<?php
db_disconnect($dbh);
?>
</html>
</body>
