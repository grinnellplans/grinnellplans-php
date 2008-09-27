<?php

/*
	GrinnellPlans - Displayfunctions
	version Nov-11-05-1 (Laiu-Draft-1)
	What this is: old parts of functions.php separated - in this page, those delaying with display to user controls.
*/

//////////

/* mdisp_beg- Looks up from the database what choices the user has for their interface and style, 
 *and gets the pathnames for the files associated with those choices. Loads the code contained in 
 *the interface page that the user basically selected, which is actually a set of a couple of 
 *functions, including mdisp_end.
*/

function mdisp_begin($dbh,$idcookie,$myurl,$myprivl,$jsfile=NULL)
{
	//get the paths of the interface and style files that the user indicated as wanting to use
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");

	$css=get_item($dbh,"stylesheet","stylesheet","userid", $idcookie);


	while($new_row = mysql_fetch_row($my_result)) {
		$mydisplayar[] = $new_row;
	}//gets contents from query

	require ($mydisplayar[0][0]);//loads up the interface functions

	if ($css) {$mycss=$css;}
	else {$mycss=$mydisplayar[0][1];}
	disp_begin($dbh,$idcookie,$myurl,$myprivl,$mycss,$jsfile);//calls the real beginning display 
	//function which actually does the work of sending the beginning html code to the user

}


/* Did text outline, did a gradiant with white and gray, with white being
on top, but less of white. Did a neon glow thing, then did an invert, the
messed with the color balance, to make it a light blue
*/

//////////

/*
 *Simple beginning to guest display
 */
function gdisp_begin($dbh)
{

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
		<form action="read.php" method="post">
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
function gdisp_end()
{echo "</td></tr></table></td></tr></table></body></html>";}


/*
function privlev($myprivl, $cookiepriv)
{
	echo $cookiepriv . "---<br>";
	echo $myprivl . "=<br>";



	if (($myprivl == "") and ($cookiepriv == ""))
	{
		return 1;
	}
	else
	{
		if ($cookiepriv == "")
		{
			//setcookie("myprivl", $myprivl,0, "/~kenslerj/plans3/");
			return $myprivl;
		}
		if ($myprivl == "")
		{return $cookiepriv;
	}
	if (($myprivl != "") and ($cookiepriv !=""))
	{//setcookie("myprivl", $myprivl,0, "/~kenslerj/plans3/");
	return $myprivl;
}
}
}
*/
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
