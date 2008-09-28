<?php
	require_once("Plans.php");

require_once("cookie_session.php");
require("functions-main.php");//load main functions
$dbh = db_connect();//get the database connection

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

$result = mysql_query("select username from accounts order by rand() limit 1");
$result_row = mysql_fetch_array($result);
$random_user = $result_row[0];


if ($myprivl)
{$myprivl = "&myprivl=" . $myprivl;}//if the priv level has been set, tack it on to the URL to keep it going

header("Location: read.php?searchname=" . $random_user . $myprivl);//Send the user to that plan

db_disconnect($dbh);


?>


