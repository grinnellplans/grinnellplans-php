<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
$dbh = db_connect(); //get the database connection
$idcookie = User::id();

$result = mysql_query("select username from accounts order by rand() limit 1");
$result_row = mysql_fetch_array($result);
$random_user = $result_row[0];
header("Location: read.php?searchname=" . $random_user); //Send the user to that plan
db_disconnect($dbh);
?>


