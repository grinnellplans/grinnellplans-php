<?php
require_once ("Plans.php");
new SessionBroker();

$mysearch = $_GET['mysearch'];

require ("functions-main.php"); //load main functions
$dbh = db_connect(); //connect to the database
$idcookie = User::id();
$context = 100; //set the number of characters around found item
if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
} //begin user display
else {
	gdisp_begin($dbh);
} //otherwise begin guest display
if (!$mysearch) //if no search query, give search form
{
?>
	<form action="search.php" method="GET">
	<input type="text" name="mysearch">
	<input type="checkbox" name="regexp" value="1">Reg. Exp.
	<input type="checkbox" name="planlove" value="1">Planlove
	<input type="hidden" name="myprivl" value="<?php
	echo $myprivl; ?>">
	<input type="submit" value="Search">
	</form>
	<?php
} else
//otherwise perform the search
{
	if ($mysearch == "_") //if they just searched for an underscore
	{
		echo "Invalid search term.";
	} //tell them it's an invalid search
	else
	//otherwise, go on with the search
	{
		if ($planlove) {
			if (!$thesearchnum = get_item($dbh, "userid", "accounts", "username", $mysearch)) {
				echo "Search could not be performed. There is no plan with that name.";
				$donotsearch = 1;
			} else {
				$plansearchname = "[" . $mysearch . "]";
				$mynamedsearch = "searchname\=" . $mysearch . "\"";
				$mysearch = "searchnum=" . $thesearchnum . "\"";
				$regexp = 0;
			}
		} //if planlove
		if (!$donotsearch) {
			if (!$idcookie) {
				$guest = "AND webview=1";
			} else {
				$guest = "";
			}
			if (!$regexp) {
				$mysearch = preg_replace("/\&/", "&amp;", $mysearch);
				$mysearch = preg_replace("/\</", "&lt;", $mysearch);
				$mysearch = preg_replace("/\>/", "&gt;", $mysearch);
				$mysearch = preg_quote($mysearch);
				if ($mynamedsearch) {
					$likeclause = "(plan LIKE '%$mysearch%' OR plan LIKE '%$mynamedsearch%')";
				} else {
					$likeclause = "plan LIKE '%$mysearch%'";
				}
				$querytext = "SELECT username, plan, userid FROM accounts
			where $likeclause $guest ORDER BY username";
				echo "<!--- $querytext --->";
				$my_result = mysql_query($querytext);
			} //if not regexp
			else {
				$querytext = "SELECT username, plan, userid FROM accounts
			where plan RLIKE '$mysearch' $guest ORDER BY username";
				echo "<!--- $querytext --->";
				$my_result = mysql_query($querytext);
			}
			echo "<ul>";
			while ($new_row = mysql_fetch_row($my_result)) {
				//$new_row[1] is the plan content
				$new_row[1] = preg_replace("/<br>/", "|br|", $new_row[1]);
				$new_row[1] = preg_replace("/<.*?>/s", "", $new_row[1]);
				$new_row[1] = preg_replace("/\|br\|/", "<br>", $new_row[1]);

				$new_row[1] = stripslashes($new_row[1]);
				if ($planlove) {
					$mysearch = $plansearchname;
				}

				$matchcount = preg_match_all("/(" . preg_quote($mysearch, "/") . ")/si", $new_row[1], $matcharray);
				$new_row[1] = preg_replace("/(" . preg_quote($mysearch, "/") . ")/si", "<b>\\1</b>", $new_row[1]);

				echo "<!--- " . $mysearch . "--->";
				echo "<!--- " . preg_quote($mysearch, "/") . "--->";
				echo "<li>[<a href=\"read.php?myprivl=" . $myprivl . "&searchname=" . $new_row[0] . "\">" . $new_row[0] . "</a>] (" . $matchcount . ")<br>";
				$start_array = array();
				$end_array = array();
				$o = 0;
				$pos = strpos($new_row[1], "<b>"); //find where matched term starts
				while ($o < $matchcount) {
					array_push($start_array, $pos - $context);
					$pos = strpos($new_row[1], "</b>", $pos) + 4;
					array_push($end_array, $pos + $context);
					$pos = strpos($new_row[1], "<b>", $pos); //find where matched term starts
					$o++;
				} //While $o<$matchnout-1
				$num = 0;
				while ($num < count($start_array) - 1) {
					if ($end_array[$num] >= $start_array[$num + 1]) {
						$end_array[$num] = $end_array[$num + 1];
						array_splice($start_array, $num + 1, 1);
						array_splice($end_array, $num + 1, 1);
					} else {
						$num++;
					}
				} //while $o<$matchcount-1
				echo "<ul>";
				$endsize = strlen($new_row[1]) - 1;
				for ($num = 0; $num < count($start_array); $num++) {
					//Produce excerpts
					if ($start_array[$num] < 0) {
						$start_array[$num] = 0;
					}
					if ($end_array[$num] > $endsize) {
						$end_array[$num] = $endsize;
					}
					//Try to start our excerpt on a space.
					$startof = strpos($new_row[1], " ", $start_array[$num]);
					if ($startof === FALSE) {
						$startof = $start_array[$num]; // If not possible to start context
						//	between words, then don't worry about it
						
					}
					//but don't look past our search match!
					if ($startof > strpos($new_row[1], "<b>", $start_array[$num])) {
						$startof = strpos($new_row[1], "<b>", $start_array[$num]);
					}
					//Try to end the excerpt on a space, but don't look too far.
					//This used to quote entire plans, if they didn't have any
					//spaces (huge planlove lists, for instance).
					$endof = strpos($new_row[1], " ", $end_array[$num]);
					if ($endof === false or $endof > $end_array[$num] + 20) {
						$endof = $end_array[$num];
						// Check for truncated <br> tags that screw stuff up
						if ((substr($new_row[1], $endof - 2, 3) == "<br" && $i = 3) || (substr($new_row[1], $endof - 1, 2) == "<b" && $i = 2) || (substr($new_row[1], $endof, 1) == "<" && $i = 1)) {
							$endof-= $i;
						}
					}
					//Don't try to read past the end of the plan.
					$endof = min($endof, $endsize);
					echo "<li>" . substr($new_row[1], $startof, $endof - $startof);
					echo "<br><Br>";
				} //while still displaying parts of plan
				echo "</ul>";
			} //while dealing with one plan that has term
			echo "</ul>";
			if (!($matchcount > 0)) {
				echo "No matches found.";
			}
?>
		<form action="search.php" method="GET">
		<input type="text" name="mysearch">
		<input type="checkbox" name="regexp" value="1">Reg. Exp.
		<input type="checkbox" name="planlove" value="1">Planlove   
		<input type="hidden" name="myprivl" value="<?php
			echo $myprivl; ?>">
		<input type="submit" value="Search">
		</form>
		<?php
		} //if is not marked as do not search
		
	} //if search is not an underscore
	
} //if something is entered to search for
if (User::logged_in()) {
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
} else {
	gdisp_end();
}
db_disconnect($dbh);
?>
