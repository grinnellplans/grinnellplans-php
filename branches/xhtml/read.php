<?php
require ("syntax-classes.php");
session_start();
require ("functions-main.php"); //load main functions
$idcookie = $_SESSION['userid'];
$dbh = db_connect(); //connect to database
$auth = $_SESSION['is_logged_in'];
$page = new PlansPage('Plan', 'readplan', PLANSVNAME, 'read.php');

if ($auth) {
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
	} //if not valid username

	interface_disp_page($page);
	db_disconnect($dbh);
	exit();
	
} //$if (!$searchnum)
//begin displaying if there is a user with name or number given
// Create the new page
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
	$page->append(new AlertText("Could not retrieve plan.", 'DB Error', true));
} else if (!$idcookie && $planinfo[0][5] != 1 && !$guest_auth) {
	$page->append(new AlertText("There is either no plan with that name or it is not viewable to guests.", 'Plan not found', false));
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
		$addform->append($item);
		$item = new FormItem('hidden', 'searchnum', $searchnum);
		$addform->append($item);
		for ($i = 0; $i < 4; $i++) {
			$item = new FormItem('radio', 'privlevel', $i);
			if ($i == 0) $item->description = 'X';
			else $item->description = "$i";
			$item->checked = $myonlist[$i];
			$addform->append($item);
		}
		$item = new FormItem('submit', NULL, 'Set Priority');
		$addform->append($item);
	}
}
interface_disp_page($page);
db_disconnect($dbh);

?>
