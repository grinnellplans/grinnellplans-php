<?php
require ("syntax-classes.php");
session_start();
require ("functions-main.php"); //load main functions
$idcookie = $_SESSION['userid'];
$dbh = db_connect(); //connect to database
$auth = $_SESSION['is_logged_in'];
//TODO fix this searching stuff
if (!$searchnum) //if no search number given
{
	if (isvaliduser($dbh, $searchname)) //if valid username, change to num
	{
		$searchnum = get_item($mydbh, "userid", "accounts", "username", $searchname);
	} else
	//if is not a valid username
	{
		if ($searchname) //if a searchname has been given
		{
			$searchname = htmlentities($searchname);
			if ($auth) {
				mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
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
						echo "<li><a href=\"read.php?searchnum=" . $partial_list[$o][0] . "\">" . $partial_list[$o][1] . "</a></li>";
						$o++;
					} //while ($partial_list [$o][0])
					echo "</ul>";
				} //if partial names
				echo "<br><br>A search of this term found:";
				basicSearch($idcookie, $dbh, $auth, 100, $searchname);
			} else {
				echo "There is either no plan with that name or it is not viewable to guests. ";
				echo 'Please  <a href="index.php">Log in</a> or <a href="register.php">Register</a>.';
			}
			if ($auth) {
				mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
			} else {
				gdisp_end();
			}
			mysql_close($dbh);
			exit();
		} else {
			if ($auth) {
				mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
			} else {
				gdisp_begin($dbh);
			}
			echo "Must enter a name";
			if ($auth) {
				mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
			} else {
				gdisp_end();
			}
			mysql_close($dbh);
			exit();
		}
	} //if not valid username
	
} //$if (!$searchnum)
//begin displaying if there is a user with name or number given
// Create the new page
$page = new PlansPage('Plan', 'readplan', PLANSVNAME, 'read.php');
if ($auth) {
	// If mark_as_read is set, do it and fix the url
	// TODO I'm dubious about this - perhaps we should only allow this passed in POST? Also, can't the value of $mark_as_read indicate which level to mark?
	$mark_as_read = $_GET['mark_as_read'];
	if ($mark_as_read) {
		mark_as_read($dbh, $idcookie, $myprivl);
	}
	//TODO add searchname instead?
	$page->url = add_param($page->url, 'searchnum', $searchnum);
	$my_result = mysql_query("Select priority From autofinger where
			owner = '$idcookie' and interest = '$searchnum'");
	$onlist = mysql_fetch_array($my_result);
	if ($onlist) {
		update_read($dbh, $idcookie, $searchnum); //mark as having been read
		setReadTime($dbh, $idcookie, $searchnum); //and mark time that was read
		$myonlist[$onlist[0]] = "checked"; //show which priority person is
		
	} else {
		$myonlist[0] = "checked"; //if not on autoread list, show is not on priority list
		
	}
	get_interface($idcookie);
	populate_page($page, $dbh, $idcookie);
} else {
	get_guest_interface();
	populate_guest_page($page);
}
//TODO should this go inside if(!$auth) ?
$guest_auth = false;
if ($guest_pass = $_GET['guest-pass']) {
	$real_pass = get_item($mydbh, "guest_password", "accounts", "userid", $searchnum);
	//error_log("JLW real pass is $real_pass");
	if ($real_pass == '') {
		$guest_auth = false;
	} else if ($real_pass == $guest_pass) {
		$guest_auth = true;
	} else {
		$guest_auth = false;
	}
}
//error_log("JLW guest auth is $guest_auth");
if (!$planinfo = get_items($mydbh, "username,pseudo,DATE_FORMAT(login, 
'%a %M %D %Y, %l:%i %p'),DATE_FORMAT(changed, 
'%a %M %D %Y, %l:%i %p'),plan,webview", "accounts", "userid", $searchnum)) //get all of persons plan info
{
	//if we failed, complain
	$page->append(new AlertText("Could not retrieve plan.", true));
} else if (!$idcookie && $planinfo[0][5] != 1 && !$guest_auth) {
	$page->append(new AlertText("There is either no plan with that name or it is not viewable to guests.", false));
} else {
	// we're good to go, display the plan
	$planinfo[0][1] = stripslashes($planinfo[0][1]);
	$planinfo[0][4] = stripslashes($planinfo[0][4]);
	if ($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
		$plantext = jumble($planinfo[0][4]);
	} else {
		$plantext = $planinfo[0][4];
	}
	$thisplan = new PlanContent($planinfo[0][0], $planinfo[0][1], $planinfo[0][2], $planinfo[0][3], $plantext);
	$page->append($thisplan);
	$page->title = '[' . $planinfo[0][0] . "]'s Plan";
	if ($auth && $searchnum != $idcookie) {
		//if is a valid user, give them the option of putting the plan on their autoread list, or taking it off
		$addform = new Form('autoreadadd', 'Set Priority');
		$thisplan->addform = $addform;
		$addform->action = 'readadd.php';
		$addform->method = 'POST';
		$item = new FormItem('hidden', 'addtolist', 1);
		$addform->appendField($item);
		$item = new FormItem('hidden', 'searchnum', $searchnum);
		$addform->appendField($item);
		for ($i = 0; $i < 4; $i++) {
			$item = new FormItem('radio', 'privlevel', $i);
			if ($i == 0) $item->description = 'X';
			else $item->description = "$i";
			$item->checked = $myonlist[$i];
			$addform->appendField($item);
		}
		$item = new FormItem('submit', NULL, 'Set Priority');
		$addform->appendField($item);
	}
}
interface_disp_page($page);
db_disconnect($dbh);
echo "<!-- $idcookie -->";
//TODO for the love of god why is this separate from the other search stuff?
function basicSearch($idcookie, $dbh, $auth, $context, $mysearch)
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
			if ($mynamedsearch) {
				$likeclause = "(plan LIKE '%$mysearch%' OR plan LIKE '%$mynamedsearch%')";
			} else {
				$likeclause = "plan LIKE '%$mysearch%'";
			}
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
					echo "<li>" . substr($new_row[1], $startof, $endof - $startof) . "</li>\n";
					echo "<br><br>";
				} //while still displaying parts of plan
				echo "</li></ul>";
			} //while dealing with one plan that has term
			echo "</ul>";
			if (!($matchcount > 0)) {
				echo "Nothing.";
			}
		} //if search is not an underscore
		
	} //make sure there are at least 3 characters before we search
	
} //function basicSearch

?>
