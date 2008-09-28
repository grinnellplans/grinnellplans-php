<?php
require_once ("Plans.php");
new SessionBroker();

require ("functions-main.php"); //load main functions
$dbh = db_connect(); //get database connections
$idcookie = User::id();
$auth = $_SESSION['is_logged_in'];
if (!$auth) //if not logged in
{
	gdisp_begin($dbh); //begin guest display
	
} else {
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //otherwise begin valid user display
	
}
if (!($mytime > 0 and $mytime < 100)) {
	$mytime = 12;
} //if time is out of acceptable period, set to 12
//give form to set how many hours back to look

?>
<form action="planwatch.php" method="POST">
<input type="text" name="mytime" value="<?php
echo $mytime
?>">
<input type="hidden" name="myprivl" value="<?php
echo $myprivl
?>">
<input type="submit" value="See Plans">
</form>
<?php
if ($auth) {
	$webview = '';
} else {
	$webview = 'and webview = 1';
}
$my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed,
'%l:%i %p, %a %M %D ') from accounts where
changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR)  
$webview
ORDER BY changed desc");
//do the query with specifying date format to be returned
echo "<table>";
//display the results of the query
while ($new_plans = mysql_fetch_row($my_planwatch)) {
	echo "<tr><td><a href=\"read.php?myprivl=" . $myprivl . "&searchname=" . $new_plans[1] . "\">" . $new_plans[1] . "</a></td><td>" . $new_plans[2] . "</td></tr>";
}
echo "</table>";
if (!$auth) //if not logged in
{
	gdisp_end(); //end guest display
	
} else {
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //end valid user display
	
}
db_disconnect($dbh);
?> 
