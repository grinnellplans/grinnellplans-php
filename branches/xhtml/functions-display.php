<?php

/*
	GrinnellPlans - Displayfunctions
	What this is: old parts of functions.php separated - in this page, those delaying with display to user controls.
*/

//////////

/* mdisp_beg- Looks up from the database what choices the user has for their interface and style, 
 *and gets the pathnames for the files associated with those choices. Loads the code contained in 
 *the interface page that the user basically selected, which is actually a set of a couple of functions.
*/

/* DEPRECATED */
function mdisp_begin($dbh,$idcookie,$myurl,$myprivl,$jsfile=NULL)
{
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");

	while($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	}//gets contents from query

	require ($mydisplayar[0][0]);//loads up the interface functions

	$css=get_item($dbh,"stylesheet","stylesheet","userid", $idcookie);
	if ($css) {$mycss=$css;}
	else {$mycss=$mydisplayar[0][1];}

	$searchname = $_GET['searchname'];

	$linkarr = cinterface_get_all_links($idcookie);
	for ($i = 1; $i < 4; $i++) { //TODO variable number
		$autofingerarr[$i-1] = cinterface_get_autoread($idcookie, $i);
	}

	//call the function which actually does the work of sending the beginning html code to the user
	interface_disp_begin($searchname, $linkarr, $autofingerarr, $myurl, $myprivl, $mycss, $jsfile);

}

function get_guest_interface() {
	require("interfaces/xhtml/xhtml.php"); //TODO hardcoding! bleh!
}

//TODO comment
function get_interface($idcookie) {
	//TODO again, clean this up, it's ugly
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");

	while($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	}//gets contents from query

	require ($mydisplayar[0][0]);//loads up the interface functions
}

//TODO comment
function populate_page(PlansPage $page, $dbh, $idcookie) {

	//TODO get rid of all this crap - it should be much simpler
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");

	while($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	}//gets contents from query

	//require ($mydisplayar[0][0]);//loads up the interface functions

	$css=get_item($dbh,"stylesheet","stylesheet","userid", $idcookie);
	if ($css) {$page->stylesheet=$css;}
	else {$page->stylesheet=$mydisplayar[0][1];}

	global $myprivl;
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

function populate_guest_page(PlansPage $page) {

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

/*
 * Returns an AutoRead object for the given priority
 */
function get_autoread($idcookie, $p) {
	$newarr = array();
	//TODO get a string for the level name from db
	$privarray = mysql_query("Select autofinger.interest,accounts.username
		From autofinger, accounts where owner = '$idcookie' and priority =
		'$p' and updated = '1' and autofinger.interest=accounts.userid");

	while($new_row = mysql_fetch_row($privarray)) {
		$autoreadlist[] = $new_row;
	}

	$ar = new AutoRead($p);
	$o=0;
	while ($autoreadlist[$o][0])
	{
		$ar->append(new PlanLink(NULL, $autoreadlist[$o][1]));
		$o++;
	}
	return $ar;
}

//TODO comments
function get_req_links() {
	$newarr = array();
	$newarr[] = new Hyperlink('mainlink_edit', 'edit.php', 'Edit Plan');
	$newarr[] = new Hyperlink('mainlink_search', 'search.php', 'Search Plans');
	$newarr[] = new Hyperlink('mainlink_prefs', 'customize.php', 'Preferences');
	$newarr[] = new Hyperlink('mainlink_logout', 'index.php?logout=1', 'Log Out');

	return $newarr;
}

function get_guest_links() {
	$newarr = array();
	$newarr[] = new Hyperlink('mainlink_search', 'search.php', 'Search Plans');
	$newarr[] = new Hyperlink('mainlink_listusers', 'listusers.php', 'List Users');
	$newarr[] = new Hyperlink('mainlink_logout', 'index.php?logout=1', 'Log Out');

	return $newarr;
}

function get_opt_links($idcookie) {
    $linkarray = mysql_query("Select avail_links.linkname, avail_links.html_code as html_code, static
    From avail_links, opt_links where   
    opt_links.userid = '$idcookie' and opt_links.linknum = avail_links.linknum");

	$newarr = array();
    while($new_row = mysql_fetch_row($linkarray)) {
        if ($new_row[2] == 'yes' ) {
			$foo = array();
			preg_match("/href=\"([^\"]+)\"/", $new_row[1], &$foo);
			$href = $foo[1]; // TODO this is silly, let's just store href in the db

			$thislink = new Hyperlink(NULL, $href, $new_row[0]);

        } else if ($new_row[0] == 'Secrets') {
            $count = count_unread_secrets($idcookie);
			$thislink = new Hyperlink('mainlink_secrets', 'anonymous.php', "Secrets ($count)");

        } else if ($new_row[0] == 'Jumble') {
            $url = $_SERVER['REQUEST_URI'];
            if($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
				// TODO build add_param into Hyperlink class?
                $url = add_param($url, 'jumbled', 'no');
                $linktext = 'unjumble';
            } else {
                $url = add_param($url, 'jumbled', 'yes');
                $linktext = 'jumble';
            }
			$thislink = new Hyperlink('mainlink_jumble', $url, $linktext);
		}
		$newarr[] = $thislink;
	}
	return $newarr;
}

function get_fingerbox() {
	$f = new Form('finger', 'Finger Plan');
	$f->action = 'read.php';
	$f->method = 'GET';

	$item = new FormItem('text', 'searchname', NULL);
	$item->datatype = Form::FIELD_TEXT; //TODO avram?
	$f->appendField($item);

	$item = new FormItem('hidden', 'myprivl', $myprivl);
	$f->appendField($item);

	$item = new FormItem('submit', NULL, 'Read');
	$f->appendField($item);

	return $f;
}

function get_linkhome() {
	$l = new Hyperlink('home', 'index.php', '');
	return $l;
}

function get_just_updated() {

	//TODO eh? is this used?
	//if time is out of acceptable period, set to 12
	if (!($mytime > 0 and $mytime <100)) {
		$mytime = 12;
	}

	//TODO simplify?
	//do the query with specifying date format to be returned
	$my_planwatch = mysql_query("select userid,username,DATE_FORMAT(changed,
		'%l:%i %p, %a %M %D ') from accounts where
		changed > DATE_SUB(NOW(), INTERVAL $mytime HOUR) and username != 'test'
		ORDER BY changed desc LIMIT 1");

	//return the results of the query
	$new_plans = mysql_fetch_row($my_planwatch);

	return new PlanLink('justupdated', $new_plans[1]);
}

/* DEPRECATED */
function mdisp_end($dbh,$idcookie,$myurl,$myprivl) {
	// currently just calls the interface function
	interface_disp_end($myurl);
}

/*
 *Simple beginning to guest display
 */
/* DEPRECATED */
function gdisp_begin($dbh)
{
	//TODO fix this - does guest even need myprivl?
	global $myprivl;

	if (!$myprivl == 2 or !$myprivl == 3)
	{$myprivl = 1;}

	if ($username) {
		$title = "$username's Plan";
	} else {
		$title = "Plans - Beta";
	}
	?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title ?></title>
		<style type="text/css">
		<!--
		body {  color: #000000; background-color: #ffffff}
		a:link {  text-decoration: none; color: #a9aaec; font-variant: small-caps;
		font-family: times; background: #ffffff}
		a:visited {  text-decoration: none; color: #a9aaec; font variant:
		small-caps;
		font-weight: bold; background-color:
		#ffffff;
		font-family: times;}
		a:hover {  color: #ffffff; text-decoration: underline; background-color:
		#a9aaec}
		p.main {color: #A9AAEC; font-variant: small-caps}

		p.sub { background: #f1f1f1; color: #000000; border-style: solid solid
		solid solid; border-width: 
		thin; border-color: #a9aaec; margin-bottom: 2px; font-variant: none}
		p.main2 {margin-left: 1cm; color: #99DC9A; font-variant: small-caps}
		p.main3 {margin-left: 2cm; color: #DC999A; font-variant: small-caps}
		p.main4 {margin-left: 3cm; color: #DC9ADC; font-variant: small-caps}



		-->
		</style>

		</head>
		<body bgcolor="#ffffff" vlink="#696aac" link="#696aac">


		<?/* The following disables guest access
		echo "<p align=\"center\">Guest access is (most likely) temporarily disabled.<br><br><br>
		<a href=\"http://grinnellplans.com/\">http://grinnellplans.com/</a></body></html>";
		db_disconnect($dbh);
		exit();
		*/?>


		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td valign="top" align="left">
		<img src="plans2.jpg">
		<Form action="read.php" method="post">
		<input name="searchname" type="text"><br>
		<input type="hidden" name="myprivl" value="<? echo $myprivl; ?>">
		<input type="submit" value="Read"></form>

		<table>

		<tr>
		<td><img src="right.gif"></td>
		<td><a href="home.php" class="main">home</a></td>
		</tr>

		<tr>
		<td><img src="right.gif"></td>
		<td><a href="listusers.php" class="main">list users</a></td>
		</tr>

		<tr>
		<td><img src="right.gif"></td>
		<td><a href="search.php" class="main">search plans</a></td>
		</tr>

		<tr>
		<td><img src="right.gif"></td>
		<td><a href="index.php" class="main">log out</a></td>
		</tr>
		</table>
		</td>
		<td>


		<table>

		<tr><td>

		<?



	}

/*
 *Even simpler end to guest display
 */
/* DEPRECATED */
function gdisp_end()
{echo "</td></tr></table></td></tr></table></body></html>";}

//TODO see what below here is deprecated
function wants_secrets ($idcookie) {
	$wants_secrets = mysql_query("Select avail_links.linknum, avail_links.html_code
	From avail_links, opt_links where avail_links.linknum = 11  and  
	opt_links.userid = $idcookie and opt_links.linknum = avail_links.linknum");

	if ($row = mysql_fetch_row($wants_secrets)) {
		return 1; 
	} else {
		return 0;
	}
}

function count_unread_secrets ($idcookie) {
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


function jumble_word ($word) {
    $l = strlen($word);
    if ($l < 4) {
        return $word;
    }
    return $word[0] . (str_shuffle(substr($word, 1, $l - 2))) . $word[$l - 1];
}

function jumble ($text) {
    preg_match_all('/(<[^>]*>)|([^<>]*)/', $text, $matches);
    $c = count($matches[0]);
    for ($i = 0; $i < $c; $i++) {
        $chunk = $matches[0][$i];
        if (preg_match('/</', $chunk)) {
            echo $chunk;
        } else {
            preg_match_all('/(\S+)/',$chunk, $words);
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

/* DEPRECATED */
function show_opt_links ($idcookie, $buf ) {

    $linkarray = mysql_query("Select avail_links.linkname, avail_links.html_code as html_code, static
    From avail_links, opt_links where   
    opt_links.userid = '$idcookie' and opt_links.linknum = avail_links.linknum");
    while($new_row = mysql_fetch_row($linkarray)) {
        if ($new_row[2] == 'yes' ) {
            ?>
            <tr>
           <?php echo $buf ?> 
            <td><?=$new_row[1]?></td>
            </tr>
            <?
        } else if ($new_row[0] == 'Secrets') {

            $count = count_unread_secrets($idcookie);

            ?>
            <tr>
           <?php echo $buf ?> 
            <td><a href="anonymous.php" class="main">Secrets (<?=$count?>)</a></td>
            </tr>
            <?
        } else if ($new_row[0] == 'Jumble') {
            $url = $_SERVER['REQUEST_URI'];

            if($_GET['jumbled'] == 'yes' || ($_COOKIE['jumbled'] == 'yes' && $_GET['jumbled'] != 'no')) {
                $url = add_param($url, 'jumbled', 'no');
                $linktext = 'unjumble';
            } else {
                $url = add_param($url, 'jumbled', 'yes');
                $linktext = 'jumble';
            }      
            ?>
            <tr>
           <?php echo $buf ?> 
            <td><a href="<?php echo $url ?>" class="main"><?php echo $linktext ?></a></td>
                </tr>
                <?
        }
    }
}



function log_tail() {
    $log_location = "chat/chat.talk";
    echo "<p>\n";
    $lines = `tail $log_location`;
    $lines = htmlentities($lines);
    $lines = preg_replace(array('/\n/'), array("<br \/>\n"), $lines);
    echo $lines;
    echo "</p>";
}   

?>
