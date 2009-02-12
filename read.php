<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
$idcookie = User::id();
$dbh = db_connect();

$searchnum = (isset($_GET['searchnum']) ? $_GET['searchnum'] : false);
$searchname = (isset($_GET['searchname']) ? $_GET['searchname'] : false);

if (!$searchnum) //if no search number given
{
	if (isvaliduser($dbh, $searchname)) //if valid username, change to num
	{
		$searchnum = get_item($dbh, "userid", "accounts", "username", $searchname);
	} else
	//if is not a valid username
	{
		if ($searchname) //if a searchname has been given
		{
			$searchname = htmlentities($searchname);
			if (User::logged_in()) {
				mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else
			//begin guest user display
			{
				gdisp_begin($dbh);
			}
			if ($idcookie) {
				//if a searchname has been given, but there is no user with that exact name, search the usernames to see which if any users have that string in their username
				$partial_list = partial_search($dbh, "userid,username", "accounts", "username", $searchname, "username");
				$part_count = count($partial_list);
				if ($part_count == 0) //if no users have that string in username, tell user
				{
					echo "User <b>" . $searchname . "</b> does not exist and there are no names with the term in them.";
				} else
				//but if there are usernames with string in them, display them
				{
					echo "User <b>" . $searchname . "</b> does not exist.<br>However there are <b>" . $part_count . "</b> names with " . $searchname . " in them.<br>These names are:<br>";
					echo "<ul>";
					$o = 0;
					while ($partial_list[$o][0]) //loop through displaying the usernames as links
					{
						echo "<li><a href=\"read.php?searchnum=" . $partial_list[$o][0] . "\">" . $partial_list[$o][1] . "</a>";
						$o++;
					} //while ($partial_list [$o][0])
					echo "</ul>";
				} //if partial names
				echo "<br><br>A search of this term found:";
				basicSearch($idcookie, $dbh, User::logged_in(), 100, $searchname);
			} else {
				echo "There is either no plan with that name or it is not viewable to guests. ";
				echo "Note that you must have cookies enabled to log in to Plans.  You may want to check that cookies are enabled if you have tried to log in and continue to see this message. ";
				echo 'Please  <a href="index.php">Log in</a> or <a href="register.php">Register</a>.';
			}
			if (User::logged_in()) {
				mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else {
				gdisp_end();
			}
			mysql_close($dbh);
			exit();
		} else {
			if (User::logged_in()) {
				mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else {
				gdisp_begin($dbh);
			}
			echo "Must enter a name";
			if (User::logged_in()) {
				mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else {
				gdisp_end();
			}
			mysql_close($dbh);
			exit();
		}
	} //if not valid username
	
} //$if (!$searchnum)
//begin displaying if there is a user with name or number given
if (User::logged_in()) {
	$my_result = mysql_query("Select priority From autofinger where
			owner = '$idcookie' and interest = '$searchnum'");
	$onlist = mysql_fetch_array($my_result);
	if ($onlist) {
		update_read($dbh, $idcookie, $searchnum); //mark as having been read
		setReadTime($dbh, $idcookie, $searchnum); //and mark time that was read
		$myonlist = $onlist[0];
	} else {
		$myonlist = "X";	 //if not on autoread list, show is not on priority list
	}
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
} else {
	gdisp_begin($dbh);
}
$guest_auth = false;
$guest_pass = (isset($_GET['guest-pass']) ? $_GET['guest-pass'] : false);
if ($guest_pass) {
	$real_pass = get_item($mydbh, "guest_password", "accounts", "userid", $searchnum);
	//error_log("JLW real pass is $real_pass");
	if ($real_pass == '') {
		$guest_auth = false;
	} else if ($real_pass == $guest_pass) {
		$guest_pass = true;
	} else {
		$guest_auth = false;
	}
}

if (!$planinfo = get_items($dbh, "username,pseudo,DATE_FORMAT(login, 
'%a %M %D %Y, %l:%i %p'),DATE_FORMAT(changed, 
'%a %M %D %Y, %l:%i %p'),plan,webview", "accounts", "userid", $searchnum)) //get all of persons plan info
{
	echo "Could not retrieve plan."; //or else complain
	$searchnum = $idcookie;
} //and set the searchnum to the persons own id, so that there will not be an autofinger box thing at the bottom of the non-existant plan
else {
	if (!$idcookie && $planinfo[0][5] != 1 && !$guest_auth) {
		echo "There is either no plan with that name or it is not viewable to guests. ";
		echo "Note that you must have cookies enabled to log in to Plans.  You may want to check that cookies are enabled if you have tried to log in and continue to see this message. ";
	} else {
		$planinfo[0][1] = stripslashes($planinfo[0][1]);
		echo "<table><tr><td><p class=\"main\">Username: </p></td><td><b>" . $planinfo[0][0] . "</b></td></tr></table>";
		echo "<table><tr><td><p class=\"main2\">Last login: </p></td><td>" . $planinfo[0][2] . "</td></tr></table>";
		echo "<table><tr><td><p class=\"main3\">Updated on: </p></td><td>" . $planinfo[0][3] . "</td></tr></table>";
		echo "<table><tr><td><p class=\"main4\">Name:</p></td><td><u>" . $planinfo[0][1] . "</u></td></tr></table>";
		$planinfo[0][4] = stripslashes($planinfo[0][4]);
		echo "<p class=\"sub\">";
		if (isset($_GET['jumbled']) && ($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no'))) {
			echo (jumble($planinfo[0][4]));
			//    $_SERVER['REQUEST_URI'] = add_param($_SERVER['REQUEST_URI'], 'jumbled', '1');
			
		} else {
			echo $planinfo[0][4];
		}
		echo "</p>";
	}
}
if (User::logged_in()) //if is a valid user, give them the option of putting the plan on their autoread list, or taking it off, and also if plan is on their autoread list, mark as read and mark time
{
	if (!($searchnum == $idcookie)) //if person is not looking at their own plan, give them a small form to set the priority of the persons plan on their autoread list
	{
?>
			<BR><BR><BR><BR><BR><center><table><tr><td><p class="sub2"><form method="POST" action="readadd.php">
			<input type="hidden" name="addtolist" value="1">
<?php $allopts = array('X', '1', '2', '3');
 foreach ($allopts as $value) {
	$real_value = (($value == 'X') ? 0 : $value);
	$checked = (($value == $myonlist) ? "checked" : "");
	echo "<input type=\"radio\" name=\"privlevel\" value=\"$real_value\" $checked>$value</input>";
}
?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="hidden" name="searchnum" value="<?php
		echo $searchnum; ?>"><input
			type="submit" value="Set Priority">&nbsp;&nbsp;
		</form></p></td></tr></table></center>
<?php
	}
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
} else {
	gdisp_end();
}
db_disconnect($dbh);
echo "<!-- $username -->";
function basicSearch($idcookie, $dbh, $_auth, $context, $mysearch)
{
	if (strlen($mysearch) < 3) {
		echo "<br>The term you entered was less than 3 characters long, so could not be searched for.";
	} else {
		if ($mysearch == "_") //if they just searched for an underscore
		{
			echo "Invalid search term.";
		} //tell them it's an invalid search
		else
		//otherwise, go on with the search
		{
			if (!$idcookie) {
				$guest = "AND webview=1";
			} else {
				$guest = "";
			}
			$mysearch = preg_replace("/\&/", "&amp;", $mysearch);
			$mysearch = preg_replace("/\</", "&lt;", $mysearch);
			$mysearch = preg_replace("/\>/", "&gt;", $mysearch);
			$mysearch = preg_quote($mysearch);
			$likeclause = "plan LIKE '%$mysearch%'";
			$querytext = "SELECT username, plan, userid FROM accounts
				where $likeclause $guest ORDER BY username";
			echo "<!--- $querytext --->";
			$my_result = mysql_query($querytext);
			echo "<ul>";
			while ($new_row = mysql_fetch_row($my_result)) {
				//$new_row[1] is the plan content
				$new_row[1] = preg_replace("/<br>/", "|br|", $new_row[1]);
				$new_row[1] = preg_replace("/<.*?>/s", "", $new_row[1]);
				$new_row[1] = preg_replace("/\|br\|/", "<br>", $new_row[1]);
				$new_row[1] = stripslashes($new_row[1]);
				$matchcount = preg_match_all("/(" . preg_quote($mysearch, "/") . ")/si", $new_row[1], $matcharray);
				$new_row[1] = preg_replace("/(" . preg_quote($mysearch, "/") . ")/si", "<b>\\1</b>", $new_row[1]);
				echo "<li>[<a href=\"read.php?searchname=" . $new_row[0] . "\">" . $new_row[0] . "</a>] (" . $matchcount . ")<br>";
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
				echo "Nothing.";
			}
		} //if search is not an underscore
		
	} //make sure there are at least 3 characters before we search
	
} //function basicSearch

?>