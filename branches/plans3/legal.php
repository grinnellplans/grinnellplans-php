<?php
require_once('Plans.php');

function last_updated_plan() {
	$mytime = 12;
	$my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed,
				  '%l:%i %p, %a %M %D ') from accounts where
				    changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR) and username != 'test'
				      ORDER BY changed desc LIMIT 1");
	//do the query with specifying date format to be returned
	//display the results of the query
	while ($new_plans = mysql_fetch_row($my_planwatch)) {
?>Do you read <a href="read.php?searchname=<?=$new_plans[1]?>"><?=$new_plans[1]?></a>, who just updated? <hr><?php
	}
}

function disclaimer() {
?>
	<hr>
	<p style="font-size: 80%">Use of the GrinnellPlans service means you have accepted the <a href="http://www.grinnellplans.com/tos"> GrinnellPlans Terms of Service agreement</a>. If you do not accept and abide by this agreement, you may not use GrinnellPlans. This agreement is subject to change without notice, so you should periodically review the most up-to-date version.You may contact us at <a href="mailto:grinnellplans@gmail.com">grinnellplans@gmail.com</a>.
<?php
}

function footer() {
	last_updated_plan();
	disclaimer();
}
?>