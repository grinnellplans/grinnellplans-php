<?php
require_once('Plans.php');
require ("functions-main.php");
?>
<?php display_header(); ?>
<?php 
if (!($mytime > 0 and $mytime < 100)) {
	$mytime = 12;
} //if time is out of acceptable period, set to 12
//give form to set how many hours back to look

?>
<form action="planwatch.php" method="POST">
<input type="text" name="mytime" value="<?=$mytime?>">
<input type="submit" value="See Plans">
</form>
<?php
if (User::logged_in()) {
	$webview = '';
} else {
	$webview = 'and webview = 1';
}
$my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed, '%l:%i %p, %a %M %D ') from accounts where changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR) $webview ORDER BY changed desc");
//do the query with specifying date format to be returned
?>
<table>
<?php
while ($new_plans = mysql_fetch_row($my_planwatch)) {
?>
<tr>
	<td><a href="read.php?searchname=<?=$new_plans[1]?>"><?=$new_plans[1]?></a></td>
	<td><?=$new_plans[2]?></td>
</tr>
<?php } ?>
</table>
<?php display_footer(); ?> 
