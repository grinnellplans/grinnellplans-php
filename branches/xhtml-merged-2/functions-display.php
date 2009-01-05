<?php
/*
GrinnellPlans - Displayfunctions
What this is: old parts of functions.php separated - in this page, those delaying with display to user controls.
*/
require_once('Plans.php');
function Redirect($url) {
	Header("Location: $url");
}

/**
 * Get the guest interface file.
 *
 * Finds the interface we use for guests and loads up the required file.
 */
function get_guest_interface()
{
	require ("interfaces/xhtml/xhtml.php"); //TODO hardcoding! bleh!
	
}
/**
 * Get the preferred interface for this user.
 *
 * Finds the interface the user has set in their preferences and loads up the required file.
 *
 * @param int $idcookie The user's id
 */
function get_interface($idcookie)
{
	//TODO again, clean this up, it's ugly
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");
	while ($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	} //gets contents from query
	require ($mydisplayar[0][0]); //loads up the interface functions
	
}
/**
 * Populate a Plans page with all the usual stuff
 *
 * Fills up the PlansPage object with all the elements that are found on every page:
 * the main panel (which holds the links, autoreads, finger form, etc), legal footer,
 * and so on
 *
 * @param PlansPage $page The PlansPage object
 * @param resource $dbh The database connection
 * @param int $idcookie The user's id
 */
function populate_page(PlansPage $page, $dbh, $idcookie)
{
	//TODO get rid of all this crap - it should be much simpler
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");
	while ($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	} //gets contents from query
	//require ($mydisplayar[0][0]);//loads up the interface functions
	$css = get_item($dbh, "stylesheet", "stylesheet", "userid", $idcookie);
	if ($css) {
		$page->stylesheet = $css;
	} else {
		$page->stylesheet = $mydisplayar[0][1];
	}
	$myprivl = get_myprivl();
	$page->autoreadpriority = $myprivl;
	$mp = new MainPanel();
	$page->mainpanel = $mp;
	$mp->fingerbox = get_fingerbox();
	$mp->linkhome = get_linkhome();
	$mp->requiredlinks = get_req_links();
	$mp->optionallinks = get_opt_links($idcookie);
	$mp->autoreads = array();
	for ($i = 1; $i <= 3; $i++) { //TODO variable number
		$mp->autoreads[] = get_autoread($idcookie, $i);
	}
	$footer = new Footer();
	$footer->doyouread = get_just_updated();
	$footer->legal = new InfoText(get_disclaimer(), NULL);
	$page->footer = $footer;
}
/**
 * Populates a Plans page for a guest
 *
 * Like {@link populate_page()}, but for guest display
 *
 * @param PlansPage $page The PlansPage object
 */
function populate_guest_page(PlansPage $page)
{
	//$css=get_item($dbh,"stylesheet","stylesheet","userid", $idcookie);
	//if ($css) {$page->stylesheet=$css;}
	$css = "styles/guest.css"; //TODO hardcoding this is ugly
	$page->stylesheet = $css;
	$mp = new MainPanel();
	$page->mainpanel = $mp;
	$mp->fingerbox = get_fingerbox();
	$mp->linkhome = get_linkhome();
	$mp->requiredlinks = get_guest_links();
	$mp->optionallinks = NULL;
	$mp->autoreads = NULL;
	$footer = new Footer();
	$footer->doyouread = NULL;
	$footer->legal = new InfoText(get_disclaimer(), NULL);
	$page->footer = $footer;
}
/**
 * Create an autoread list for the given priority
 *
 * @param int $p The priority level to retrieve
 * @param int $idcookie The user's id
 * @return Autoread
 */
function get_autoread($idcookie, $p)
{
	$newarr = array();
	//TODO get a string for the level name from db
	$privarray = mysql_query("Select autofinger.interest,accounts.username
		From autofinger, accounts where owner = '$idcookie' and priority =
		'$p' and updated = '1' and autofinger.interest=accounts.userid");
	while ($new_row = mysql_fetch_row($privarray)) {
		$autoreadlist[] = $new_row;
	}
	$ar = new AutoRead($p);
	$o = 0;
	while ($autoreadlist[$o][0]) {
		$ar->append(new PlanLink($autoreadlist[$o][1]));
		$o++;
	}
	return $ar;
}
/**
 * Get the required links
 *
 * These are the links that show up for every user
 *
 * @return array An array of Hyperlink objects
 */
function get_req_links()
{
	$newarr = array();
	$newarr[] = new Hyperlink('mainlink_edit', true, 'edit.php', 'Edit Plan');
	$newarr[] = new Hyperlink('mainlink_search', true, 'search.php', 'Search Plans');
	$newarr[] = new Hyperlink('mainlink_prefs', true, 'customize.php', 'Preferences');
	$newarr[] = new Hyperlink('mainlink_logout', true, 'index.php?logout=1', 'Log Out');
	return $newarr;
}
/**
 * Get the required links for a guest
 *
 * @return array An array of Hyperlink objects
 */
function get_guest_links()
{
	$newarr = array();
	$newarr[] = new Hyperlink('mainlink_search', true, 'search.php', 'Search Plans');
	$newarr[] = new Hyperlink('mainlink_listusers', true, 'listusers.php', 'List Users');
	$newarr[] = new Hyperlink('mainlink_logout', true, 'index.php?logout=1', 'Log Out');
	return $newarr;
}
/**
 * Get the optional links for a user
 *
 * These are the links that a user may enable or disable. 
 * Gets the links that the given user has enabled.
 *
 * @param int $idcookie The user's id
 * @return array An array of Hyperlink objects
 */
function get_opt_links($idcookie)
{
	$linkarray = mysql_query("Select avail_links.linkname, avail_links.html_code as html_code, static
    From avail_links, opt_links where   
    opt_links.userid = '$idcookie' and opt_links.linknum = avail_links.linknum");
	$newarr = array();
	while ($new_row = mysql_fetch_row($linkarray)) {
		if ($new_row[2] == 'yes') {
			$foo = array();
			preg_match("/href=\"([^\"]+)\"/", $new_row[1], &$foo);
			$href = $foo[1]; // TODO this is silly, let's just store href in the db
			$thislink = new Hyperlink('opt_link', false, $href, $new_row[0]);
		} else if ($new_row[0] == 'Secrets') {
			$count = count_unread_secrets($idcookie);
			$thislink = new Hyperlink('mainlink_secrets', true, 'anonymous.php', "Secrets ($count)");
		} else if ($new_row[0] == 'Jumble') {
			$url = $_SERVER['REQUEST_URI'];
			if ($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
				// TODO build add_param into Hyperlink class?
				$url = add_param($url, 'jumbled', 'no');
				$linktext = 'unjumble';
			} else {
				$url = add_param($url, 'jumbled', 'yes');
				$linktext = 'jumble';
			}
			$thislink = new Hyperlink('mainlink_jumble', true, $url, $linktext);
		}
		$newarr[] = $thislink;
	}
	return $newarr;
}
/**
 * Gets the finger form
 *
 * This is the form that users may use to read a plan by typing a username
 *
 * @return Form
 */
function get_fingerbox()
{
	$f = new Form('finger', true, 'Finger Plan');
	$f->action = 'read.php';
	$f->method = 'GET';
	$item = new TextInput('searchname', NULL);
	$item->datatype = Form::FIELD_TEXT; // Setting expected datatype.
	$f->append($item);
	$item = new HiddenInput('myprivl', $myprivl);
	$f->append($item);
	$item = new SubmitInput('Read');
	$f->append($item);
	return $f;
}
/**
 * Get a link to the Plans homepage
 * @return Hyperlink
 */
function get_linkhome()
{
	$l = new Hyperlink('home', true, 'index.php', '');
	return $l;
}
/**
 * Get a link to the most recently updated plan. 
 * @return Hyperlink
 */
function get_just_updated()
{
    // Get the most recently updated plan
    $my_planwatch = mysql_query("SELECT userid, username 
                            FROM accounts
                            WHERE username != 'test'
		            ORDER BY changed DESC LIMIT 1");
    //return the results of the query
    $new_plans = mysql_fetch_row($my_planwatch);
    // Get the appropriate URI for a plan link
    $temp = new PlanLink($new_plans[1]);
    // But we want a generic Hyperlink, so it can be styled separately.
    return new Hyperlink('justupdated', true, $temp->href, $new_plans[1]);
}

//TODO see what below here is deprecated
function wants_secrets($idcookie)
{
	$wants_secrets = mysql_query("Select avail_links.linknum, avail_links.html_code
	From avail_links, opt_links where avail_links.linknum = 11  and  
	opt_links.userid = $idcookie and opt_links.linknum = avail_links.linknum");
	if ($row = mysql_fetch_row($wants_secrets)) {
		return 1;
	} else {
		return 0;
	}
}
function count_unread_secrets($idcookie)
{
	$last_viewed = mysql_query("select date from viewed_secrets where userid = $idcookie");
	if ($date_row = mysql_fetch_array($last_viewed)) {
		$last = $date_row['date'];
	} else {
		$last = "000-00-00 00:00:00";
	}
	$sql = "select count(*) as n from secrets where display = 'yes' and secrets.date_approved > '$last'";
	$count = mysql_query($sql);
	$count_row = mysql_fetch_array($count);
	$count = $count_row['n'];
	return $count;
}
function jumble_word($word)
{
	$l = strlen($word);
	if ($l < 4) {
		return $word;
	}
	return $word[0] . (str_shuffle(substr($word, 1, $l - 2))) . $word[$l - 1];
}
function jumble($text)
{
	preg_match_all('/(<[^>]*>)|([^<>]*)/', $text, $matches);
	$c = count($matches[0]);
	for ($i = 0; $i < $c; $i++) {
		$chunk = $matches[0][$i];
		if (preg_match('/</', $chunk)) {
			echo $chunk;
		} else {
			preg_match_all('/(\S+)/', $chunk, $words);
			$word_count = count($words[0]);
			//echo $word_count;
			//echo "\n";
			for ($j = 0; $j < $word_count; $j++) {
				//echo ($words[0][$j]);
				echo (jumble_word($words[0][$j]));
				echo "\n";
			}
		}
	}
}
?>
