<?php
$plan = '


th [http://myspace.com/everettdean|ABCDE]!</b>
<b>NEW: Rockabilly rocker [http://myspace.com/everettdean|ABCDE] of Chicago will be playing for the night! It\'s definitely going to be
';

	$somearray = preg_match_all("/.*?\[(.*?)\].*?/s", $plan, $mymatches);//get an array of everything in brackets
print_r ($mymatches);

	$matchcount = count($mymatches[1]);
	for ($o=0; $o<$matchcount; $o++)//do a loop to test whether everything in brackets is a valid user or not
{
	$mycheck=$mymatches[1][$o];//get the current thing being tested

	//echo '<!-- ' ."/\[$mycheck\]/s" . ' -->' . "\n";
	$jlw = preg_replace("/\//", '\/', $mycheck); 
	//echo '<!-- ' ."/\[$jlw\]/s" . ' -->' . "\n";

	if (!$checked[$mycheck])//make sure current thing being checked has not already been checked
	{
		//check for plan with username
	}
}
?>
