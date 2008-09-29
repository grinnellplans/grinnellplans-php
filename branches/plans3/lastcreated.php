<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect();
$idcookie = User::id();
if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
	echo "<b><u>Plans created within the last 5 days:</u></b><br><br><ul>";
	$my_result = mysql_query("Select userid,username,DATE_FORMAT(created,'%l:%i %p, %a %M %D ') From accounts where created > DATE_SUB(NOW(), 
		INTERVAL 5
		DAY) ORDER BY created desc");
	while ($new_row = mysql_fetch_row($my_result)) {
		echo "<li><a href=\"read.php?searchnum=" . $new_row[0] . "&myprivl=" . $myprivl . "\">" . $new_row[1] . "</a>";
		echo "<ul><li>" . $new_row[2] . "</ul>";
	}
	echo "</ul>";
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
} else {
	echo "<html><body>Nothing to see here.</body></html>";
}
db_disconnect($dbh);
?>
