<?php
/*
	GrinnellPlans - legal disclaimer file
	Version Nov-11-05-1 (Laiu-Draft-1)
*/

/*
 * who_just_updated()
 * Return the planname of the most recent person to update their plan.
 */
function who_just_updated() {
	//TODO put this somewhere more sensible
	//TODO now this is just deprecated, remove

	//if time is out of acceptable period, set to 12
	if (!($mytime > 0 and $mytime <100)) {
		$mytime = 12;
	}

	//do the query with specifying date format to be returned
	$my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed,
		'%l:%i %p, %a %M %D ') from accounts where
		changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR) and username != 'test'
		ORDER BY changed desc LIMIT 1");

	//return the results of the query
	$new_plans = mysql_fetch_row($my_planwatch);

	return $new_plans[1];
}

function disclaimer()
{

	?>
	<hr>
	<p style="font-size: 80%">

Use of the GrinnellPlans service means you have accepted the <a href="http://www.grinnellplans.com/tos"> GrinnellPlans Terms of Service agreement</a>. If you do not accept and abide by this agreement, you may not use GrinnellPlans. This agreement is subject to change without notice, so you should periodically review the most up-to-date version.
You may contact us at 
<script language="JavaScript" type="text/javascript">
<!--
var name = "grinnellplans";
var domain = "gmail.com";
document.write('<a href="mailto:' + name + '&#64;' + domain + '">');
 document.write(name + '&#64;' + domain);
document.write('</a>');
// -->
</script>

	<?php
}

/* Same as above, only returns it as a string */
function get_disclaimer() {
	return "Use of the GrinnellPlans service means you have accepted the <a href=\"http://www.grinnellplans.com/tos\"> GrinnellPlans Terms of Service agreement</a>. If you do not accept and abide by this agreement, you may not use GrinnellPlans. This agreement is subject to change without notice, so you should periodically review the most up-to-date version.
You may contact us at 
<script language=\"JavaScript\" type=\"text/javascript\">
<!--
var name = \"grinnellplans\";
var domain = \"gmail.com\";
document.write('<a href=\"mailto:' + name + '&#64;' + domain + '\">');
 document.write(name + '&#64;' + domain);
document.write('</a>');
// -->
</script>";
}

?>
