<?php
/**
 * @todo This should not be its own page. Or at the very least should not
 * duplicate all of read.php.
 */
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
require('syntax-classes.php');
$dbh = db_connect(); //connect to the database
$idcookie = User::id();
$page = new PlansPage('Plan', 'readadd', PLANSVNAME, 'readdd.php');

if (User::logged_in()) {
	get_interface($idcookie);
	populate_page($page, $dbh, $idcookie);
} else
	//begin guest user display
{
	get_guest_interface();
	populate_guest_page($page);
}

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
			if ($idcookie) {
				//if a searchname has been given, but there is no user with that exact name, search the usernames to see which if any users have that string in their username
				$partial_list = partial_search($dbh, "userid,username", "accounts", "username", $searchname, "username");
				$part_count = count($partial_list);
				if ($part_count == 0) //if no users have that string in username, tell user
				{
					$nouser = new AlertText("User <b>$searchname</b> does not exist and there are no names with the term in them.", 'No such user');
					$page->append($nouser);
				} else
				//but if there are usernames with string in them, display them
				{
					$nouser = new AlertText("User <b>$searchname</b> does not exist.<br>However there are <b>$part_count</b> names with $searchname in them.<br>These names are:", 'No such user');;
					$page->append($nouser);
					$namelist = new WidgetList('partial_name_matches', true);
					$page->append($namelist);
					$o = 0;
					while ($partial_list[$o][0]) //loop through displaying the usernames as links
					{
						$name = new PlanLink($partial_list[$o][1]);
						$namelist->append($name);
						$o++;
					} //while ($partial_list [$o][0])
				} //if partial names
				$searchlink = new Hyperlink('search_for_term', true, "search.php?mysearch=$searchname", "Search Plans for \"$searchname\"");
				$page->append($searchlink);
			} else {
				$err = new AlertText('There is either no plan with that name or it is not viewable to guests.' . 
					' Please <a href="index.php">Log in</a> or <a href="register.php">Register</a>.', 'Plan not available');
				$page->append($err);
			}
		} else {
			$err = new AlertText('Must enter a name', 'Input needed');
			$page->append($err);
		}

		interface_disp_page($page);
		db_disconnect($dbh);
		exit();
	} //if not valid username
	
} //$if (!$searchnum)

if (!$planinfo = get_items($mydbh, "username,pseudo,DATE_FORMAT(login, 
'%a %M %D %Y, %l:%i %p'),DATE_FORMAT(changed, 
'%a %M %D %Y, %l:%i %p'),plan,webview", "accounts", "userid", $searchnum)) //get all of persons plan info
{
	//if we failed, complain
	$page->append(new AlertText("Could not retrieve plan.", 'DB Error', true));
	$searchnum = $idcookie;
} //and set the searchnum to the persons own id, so that person does not get option to add non-existant user/plan to their autoread list
//this was done because deleted plans would still give the option to add the page to autoread list (until I add in checking to make sure is a valid usernumber)
else { //if data was successfully retrieved
	// we're good to go, display the plan
	$planinfo[0][1] = stripslashes($planinfo[0][1]);
	$planinfo[0][4] = stripslashes($planinfo[0][4]);
	if ($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
		$plantext = jumble($planinfo[0][4]);
	} else {
		$plantext = $planinfo[0][4];
	}
	$plantext = new PlanText($plantext, false);
	$thisplan = new PlanContent($planinfo[0][0], $planinfo[0][1], $planinfo[0][2], $planinfo[0][3], $plantext);
	$page->append($thisplan);
	$page->title = '[' . $planinfo[0][0] . "]'s Plan";
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
				$yay = new InfoText("User " . $planinfo[0][0] . " added to autoread list with priority level of " . $privlevel . "."); //inform user of the change
				$page->append($yay);
				
			}
		}
	}
}

interface_disp_page($page);
db_disconnect($dbh);
}
?>
