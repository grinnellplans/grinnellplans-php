<?php
	require_once("Plans.php");
function disclaimer()
{

	//Let's try a 'new plan try reading' feature.
	if (!($mytime > 0 and $mytime <100))
	{
		          $mytime = 12;
			    }//if time is out of acceptable period, set to 12

			      //give form to set how many hours back to look

			        $my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed,
				  '%l:%i %p, %a %M %D ') from accounts where
				    changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR) and username != 'test'
				      ORDER BY changed desc LIMIT 1");
				        //do the query with specifying date format to be returned


					  //display the results of the query
					    while($new_plans = mysql_fetch_row($my_planwatch)) {
						                echo "Do you read " . "<a href=\"read.php?myprivl=" . $myprivl .
								                "&searchname=" .
										                    $new_plans[1] . "\">" . $new_plans[1] . "</a>" . ", who just updated?" . "<hr>";
												                }
														//End 'new plan try reading' feature.
														
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

?>
