<?php
require_once('Plans.php');
//////////
/*
*Set the time when the plan was updated
*/
function setUpdatedTime($idcookie)
{
	$t = timestamp();
	mysql_query("UPDATE accounts SET changed = $t WHERE userid = $idcookie");
}
//////////
/*
*Get all of the plans that fall between two letters (usually the same one)
*/
function get_letters($dbh, $first_letter, $second_letter, $idcookie)
{
	if (!$idcookie) {
		$guest = "AND webview=1";
	}
	$my_result = mysql_query("Select userid,username From accounts where username > '$first_letter' and username < '$second_letter' $guest ORDER BY username");
	while ($new_row = mysql_fetch_row($my_result)) {
		$all[] = $new_row;
	}
	return $all;
}
/*
*Handles the cleaning up of a plan, such as allowing only certain html links in
*/
function cleanText($plan)
{
	$plan = htmlspecialchars($plan); //take out html
	//fix the dollar sign error- by josh
	//$plan = preg_replace("(\|(\w\s)*)\$
	$plan = preg_replace("/((\[\w*\],?){8})(?=[^ ,])/s", "$1 ", $plan);
	$plan = preg_replace("/\n/s", "<br>", $plan);
	$plan = preg_replace("/\&lt\;hr\&gt\;/si", "<hr><p class=\"sub\">", $plan);
	$plan = preg_replace("/\&lt\;b\&gt\;(.*?)\&lt\;\/b\&gt\;/si", "<b>\\1</b>", $plan); //allow stuff in the bold tag back in
	$plan = preg_replace("/\&lt\;tt\&gt\;(.*?)\&lt\;\/tt\&gt\;/si", "<tt>\\1</tt>", $plan);
	$plan = preg_replace("/\&lt\;pre\&gt\;(.*?)\&lt\;\/pre\&gt\;/si", "<pre>\\1</pre>", $plan);
	$plan = preg_replace("/\&lt\;strike\&gt\;(.*?)\&lt\;\/strike\&gt\;/si", "<strike>\\1</strike>", $plan);
	$plan = preg_replace("/\&lt\;s\&gt\;(.*?)\&lt\;\/s\&gt\;/si", "<s>\\1</s>", $plan);
	$plan = preg_replace("/\&lt\;i\&gt\;(.*?)\&lt\;\/i\&gt\;/si", "<i>\\1</i>", $plan); //allow stuff in the italics tag back in
	$plan = preg_replace("/\&lt\;u\&gt\;(.*?)\&lt\;\/u\&gt\;/si", "<u>\\1</u>", $plan); //allow stuff in the underline tag back in
	$plan = preg_replace("/\&lt\;a.+?href=.&quot\;(.+?).&quot\;&gt\;(.+?)&lt\;\/a&gt\;/si", "<a href=\"\\1\" class=\"onplan\">\\2</a>", $plan);
	//$plan = preg_replace("/\&lt\;a.href=.&quot\;(.+).&quot\;/si", "EEE",$plan);
	$somearray = preg_match_all("/.*?\[(.*?)\].*?/s", $plan, $mymatches); //get an array of everything in brackets
	$matchcount = count($mymatches[1]);
	for ($o = 0; $o < $matchcount; $o++) //do a loop to test whether everything in brackets is a valid user or not
	{
		$mycheck = $mymatches[1][$o]; //get the current thing being tested
		//echo '<!-- ' ."/\[$mycheck\]/s" . ' -->' . "\n";
		$jlw = preg_replace("/\//", '\/', $mycheck);
		//echo '<!-- ' ."/\[$jlw\]/s" . ' -->' . "\n";
		if (!isset($checked[$mycheck])) //make sure current thing being checked has not already been checked
		{
			//check for plan with username
			$dbh = db_connect();
			if ($item = get_item($dbh, "username", "accounts", "username", $mycheck)) //see if is a valid user, if so also gets username
			{
				$plan = preg_replace("/\[$mycheck\]/s", "[<a href=\"read.php?searchname=$item\" class=\"planlove\">$mycheck</a>]", $plan); //change all occurences of person on plan
				
			} else {
				if (preg_match('/^\d+$/', $mycheck) && $item = get_item($dbh, "messageid", "subboard", "messageid", $mycheck)) {
					$plan = preg_replace("/\[" . preg_quote($mycheck, "/") . "\]/s", "[<a href=\"board_messages.php?messagenum=$item#$item\" class=\"boardlink\">$mycheck</a>]", $plan);
				}
				if ($mycheck == "dnew") {
					$plan = preg_replace("/\[dnew\]/s", "<b>" . date("F j, Y, l H:i") . "</b>", $plan);
				}
				if ($mycheck == "date") {
					$plan = preg_replace("/\[date\]/s", "<b>" . date("l F j, Y. g:i A") . "</b>", $plan);
				}
				if (strrpos($mycheck, ":")) {
					if (strrpos($mycheck, "|")) {
 						preg_match("/(.+?)\|(.+)/si",$mycheck,$love_replace);
 						// Here, we need to escape $'s so they don't get treated as back-references
 						$love_replace[2]=addcslashes($love_replace[2],"$");
 						$plan=preg_replace("/\[" . preg_quote($mycheck,"/") . "\]/s", "<a href=\"$love_replace[1]\" class=\"onplan\">$love_replace[2]</a>",$plan);
					} else {
						$plan = preg_replace("/\[" . preg_quote($mycheck, "/") . "\]/s", "<a href=\"$mycheck\" class=\"onplan\">$mycheck</a>", $plan);
					}
				}
			}
			$checked[$mymatches[1][$o]] = 1; //mark checked values as checked, so don't have to check again
			
		} //if (!$checked[$mycheck])
		
	} //for ($o=0; $mymatches[1][$o]; $o++)
	$plan = trim($plan);
	return $plan;
}
?>
