<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
$dbh = db_connect(); //connect to the database
$idcookie = User::id();

if (isset($_POST['searchnum'])) //if no search number given
{
	$searchnum = $_POST['searchnum'];
	$searchname = (isset($_POST['searchname']) ? $_POST['searchname'] : false);
	if (isvaliduser($dbh, $searchname)) //if valid username, change to number
	{
		$searchnum = get_item($dbh, "userid", "accounts", "username", $searchname);
	} else {
		if ($searchname) //if there is no user number given, but there is a name, but not an exact one of a user, try to search for similar ones
		{
			if (User::logged_in()) //if is valid user, begin user display
			{
				mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else
			//otherwise is a guest, so beging guest display
			{
				gdisp_begin($dbh);
			}
			$partial_list = partial_search($dbh, "userid,username", "accounts", "username", $searchname, "username"); //try to find usernames that contain the string provided
			$part_count = count($partial_list); //tally the number of usernames that containt the string
			if ($part_count == 0) //if there are no matches, say so
			{
				echo "User <b>" . $searchname . "</b> does not exist and there are no names with 
the term in them.";
			} else
			//else there are matches, so give results
			{
				echo "User <b>" . $searchname . "</b> does not exist.<br>However there are <b>" . $part_count . "</b> names with " . $searchname . "in them.<br>These names are:<br>";
				echo "<ul>";
				$o = 0;
				while ($partial_list[$o][0]) //loop through the array of usernames that contain string, printing each one nicely.
				{
					echo "<li><a href=\"read.php?searchnum=" . $partial_list[$o][0] . "\">" . $partial_list[$o][1] . "</a>";
					$o++;
				} //while ($partial_list [$o][0])
				echo "</ul>";
			} //if partial names
			if (User::logged_in()) {
				mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} //end valid user display
			else {
				gdisp_end();
			} //end guest user display
			mysql_close($dbh);
			exit();
		} //end page processing, since we couldn't determine user number
		else
		//user has submitted form, but with no user number or username
		{
			if (User::logged_in()) {
				mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} //begin valid user display
			else {
				gdisp_begin($dbh);
			} //
			echo "Must enter a name";
			if (User::logged_in()) {
				mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
			} else {
				gdisp_end();
			}
			mysql_close($dbh);
			exit();
		} //end page processing, since we couldn't determine user number
		
	} //if not valid username
	
} //$if (!$searchnum)
//if we are at this point, we've determined a valid user number
if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
} //begin valid user display
else {
	gdisp_begin($dbh);
} //begin guest user display
//get plan data or complain if not possible for whatever reason
if (!$planinfo = get_items($dbh, "username,pseudo,DATE_FORMAT(login,
'%a %M %D, %l:%i %p'),DATE_FORMAT(changed,
'%a %M %D, %l:%i %p'),plan", "accounts", "userid", $searchnum)) {
	echo "Could not retrieve plan.";
	$searchnum = $idcookie;
} //and set the searchnum to the persons own id, so that person does not get option to add non-existant user/plan to their autoread list
//this was done because deleted plans would still give the option to add the page to autoread list (until I add in checking to make sure is a valid usernumber)
else { //if data was successfully retrieved
	$planinfo[0][1] = stripslashes($planinfo[0][1]); //strip slashes that were added in to make the pseudo name safe for the database
	echo "<table><tr><td><p class=\"main\">Plan of:</p></td><td>" . $planinfo[0][0] . "</td></tr></table>";
	echo "<table><tr><td><p class=\"main2\">Last log in:</p></td><td>" . $planinfo[0][2] . "</td></tr></table>";
	echo "<table><tr><td><p class=\"main3\">Last update:</p></td><td>" . $planinfo[0][3] . "</td></tr></table>";
	echo "<table><tr><td><p class=\"main4\">Name:</p></td><td>" . $planinfo[0][1] . "</td></tr></table>";
	$planinfo[0][4] = stripslashes($planinfo[0][4]); //strip slashes that were added in to make the plan safe for the database
	echo "<p class=\"sub\">";
	echo $planinfo[0][4]; //display the plan
	echo "</p>";
}
if (User::logged_in()) //if valid user, check to see if plan is on their autoread list, if so update appropriately
{
	$my_result = mysql_query("Select owner From autofinger where
owner = '$idcookie' and interest = '$searchnum'"); //try to get data of if plan is on users autoread list
	$onlist = mysql_fetch_array($my_result);
	if ($onlist) //if plan is in users autoread list
	{
		update_read($dbh, $idcookie, $searchnum); //mark as read
		setReadTime($dbh, $idcookie, $searchnum); //and mark the time
		if ($addtolist == 1) //if person is changing which tier plan is on their autoread list
		{
			if ($privlevel == 0) //if is set to privlev 0, plan was on autoread list, but user no longer wants them on autoread list at all
			{
				mysql_query("DELETE FROM autofinger WHERE
owner = '$idcookie' and interest = '$searchnum'");
			} //remove plan from users autoread list
			else {
				mysql_query("UPDATE autofinger SET priority = '$privlevel'
WHERE owner = '$idcookie' AND interest = '$searchnum'"); //otherwise person just wants to change which tier plan is on, so change it
				
			}
		}
	} else
	//if person is not on users autoread list already
	{
		if ($addtolist == 1) //if wanting to change tier level of plan
		{
			if (!$privlevel == 0) //make sure that person has set an actual tier level to set to, otherwise don't do anything since they don't want the plan on their autoread list.
			{
				$rew = array($idcookie, $searchnum, $privlevel, "", "", "");
				add_row($dbh, "autofinger", $rew); //add plan to autoread list.
				echo "<table><tr><td><center><p class=\"sub2\">User " . $planinfo[0][0] . " added to autoread list with priority level of " . $privlevel . ".</p></center></td></tr></table>"; //inform user of the change
				
			}
		}
	}
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //end valid user display
	
} else {
	gdisp_end();
} 
db_disconnect($dbh);
?>
