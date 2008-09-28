<?php
/* Some common functions for use by interfaces as desired. */
/**
 * TODO rewrite
 * Function: get_opt_links ($idcookie, $front, $end)
 * Params: the the Plans id cookie, plus strings $front and $end.
 * Returns: the optional links for the user. Each link is wrapped with $front and $end.
 */
function cinterface_get_opt_links($idcookie)
{
	$newarr = array();
	$linkarray = mysql_query("Select avail_links.linkname, avail_links.html_code as html_code, static
    From avail_links, opt_links where   
    opt_links.userid = '$idcookie' and opt_links.linknum = avail_links.linknum");
	$i = - 1;
	while ($new_row = mysql_fetch_row($linkarray)) {
		if ($new_row[2] == 'yes') {
			$newarr[++$i] = $new_row[1];
		} else if ($new_row[0] == 'Secrets') {
			$count = count_unread_secrets($idcookie);
			$newarr[++$i] = "<a href=\"anonymous.php\" class=\"main\">Secrets (" . $count . ")</a>";
		} else if ($new_row[0] == 'Jumble') {
			$url = $_SERVER['REQUEST_URI'];
			if ($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
				$url = add_param($url, 'jumbled', 'no');
				$linktext = 'unjumble';
			} else {
				$url = add_param($url, 'jumbled', 'yes');
				$linktext = 'jumble';
			}
			$newarr[++$i] = "<a href=\"" . $url . "\" class=\"main\">" . $linktext . "</a>";
		}
	}
	return $newarr;
}
/**
 * TODO rewrite
 * Function: get_all_links ($idcookie, $front, $end)
 * Params: the the Plans id cookie, plus strings $front and $end.
 * Returns: all Plan sidebar links for the user. Each link is wrapped with $front and $end.
 */
function cinterface_get_all_links($idcookie)
{
	$linkarr = array();
	$i = 0;
	// some required links
	$linkarr[$i] = "<a href=\"edit.php\" class=\"main\">edit plan</a>";
	$linkarr[++$i] = "<a href=\"search.php\" class=\"main\">search plans</a>";
	$linkarr[++$i] = "<a href=\"customize.php\" class=\"main\">preferences</a>";
	// add all the optional links
	$optarr = cinterface_get_opt_links($idcookie);
	for ($j = 0; $optarr[$j]; $j++) {
		$linkarr[++$i] = $optarr[$j];
	}
	// and the logout link
	$linkarr[++$i] = "<a href=\"index.php?logout=1\" class=\"main\">log out</a>";
	return $linkarr;
}
/*
* TODO rewrite
* Returns the autoread list of the given priority as a string.
* Entries take the form of <a href="URL">username</a> wrapped on either side
* by strings $front and $end.
*/
function cinterface_get_autoread($idcookie, $priority)
{
	$newarr = array();
	//TODO get a string for the level name from db
	$privarray = mysql_query("Select autofinger.interest,accounts.username
		From autofinger, accounts where owner = '$idcookie' and priority =
		'$priority' and updated = '1' and autofinger.interest=accounts.userid");
	while ($new_row = mysql_fetch_row($privarray)) {
		$autoreadlist[] = $new_row;
	}
	$i = 0;
	$newarr[$i] = "Level " . $priority;
	$o = 0;
	while ($autoreadlist[$o][0]) {
		$read_url = 'read.php';
		$read_url = add_param($read_url, 'searchname', $autoreadlist[$o][1]);
		$newarr[++$i] = "<a href=\"" . $read_url . "\">" . $autoreadlist[$o][1] . "</a>";
		$o++;
	}
	return $newarr;
}
/**
 * TODO
 */
function cinterface_get_preferences_list()
{
	// For now, just hard code this because it's static
	// TODO capitalize?
	$arr = array('change auto list' => "autoread.php", 'change password' => "changepassword.php", 'change name' => "changename.php", 'guest readable' => "webview.php", 'customize' => 'nolink', 'interfaces' => "interfaces.php", 'styles' => "styles.php", 'edit text box size' => "textbox.php", 'optional links' => "links.php");
	return $arr;
}
