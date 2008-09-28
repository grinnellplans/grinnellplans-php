<?php
	require_once("Plans.inc");


require("dbfunctions.php");//load up the set of functions that deals with the database

$TZ='America/Chicago';
putenv ("TZ=$TZ");

function disclaimer() {
	?>
	<hr>
	<p style="font-size: 60%">

	You are deemed to have accepted this Agreement upon use of the GrinnellPlans system. This agreement may change at any time without prior notice. The creators and managers of Plans take no responsibility for comments made on the Plans system. You are responsible for the content of your plan. You agree to defend, indemnify, and hold harmless, individually and collectivelly, GrinnellPlans and its administrators from and against all liabilities, costs and expenses related to or arising from any violation of applicable laws or this Agreement by you. During all times users must comply with federal and California state law. Upon use of this service, you consent to the exclusive personal jurisdiction of and venue in a court located in Honolulu, Hawaii for any suits or causes of action connected in any way, directly or indirectly, to the subject matter of this agreement or to the service. 
	The creators and managers of Plans reserve the right to remove, modify an individual's plan, and terminate service without prior notice. No guarantees are made as to the reliability and security of any information transmitted on this system. You assume all risk and responsibility for use of this service.  
	Please send questions, comments, reports of software errors, and 
	reports of violations of federal or California state laws to 
	<script language="JavaScript" type="text/javascript">
	<!--
	var name = "grinnellplans";
	var domain = "gmail.com";
	document.write('<a href="mailto:' + name + '&#64;' + domain + '">');
	document.write(name + '&#64;' + domain);
	document.write('</a>');
	// -->
	</script>
	<? 
}

function setpriv($myprivl, $cookpriv) {
	if ($myprivl)
	{
		if ($myprivl != $cookpriv)
		{
			setcookie("thepriv", $myprivl,0, "");
		}
		return $myprivl;
	}
	else {
		return $cookpriv;
	}
}



//////////////////////////////////////////////////////////////

/* mdisp_beg- Looks up from the database what choices the user has for their interface and style, 
 *and gets the pathnames for the files associated with those choices. Loads the code contained in 
 *the interface page that the user basically selected, which is actually a set of a couple of 
 *functions, including mdisp_end.
*/

function mdisp_begin($dbh,$idcookie,$myurl,$myprivl,$jsfile=NULL)
{
	$my_result = mysql_query("Select interface.path,style.path From
	interface interface, style style,display display where
	display.userid = '$idcookie' and display.interface = interface.interface and display.style=style.style");//get the paths of the interface and style files that the user indicated as wanting to use

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


////////////////////////////////////////////////////////////////////
function threadsperpage()
{return 25;}

function messagesperpage()
{return 25;}

////////////////////////////////////////////////////////////////////
/*
 *Check to see if is a valid user, displays boolean 1 or 0.
 */
 function isvaliduser($dbh, $username){
	 if
	 (!get_items($mydbh,"username","accounts","username",
	 $username)) {return 0;} else {return 1;}
 }

////////////////////////////////////////////////////////////////////
/*
 * Check to see if it is a valid userid/password combination, returns
 * boolean true if valid
 */
function isvalidauth($dbh, $idcookie, $password)
{

    if ( !$idcookie || !$password )
        return( 0 );
    $password = crypt($password, "ab"); //encrypt the password, change to ab
    $read_pass = get_item($dbh, "password", "accounts", "userid", $idcookie);
    return( $read_pass && $read_pass == $password );
}

/////////////////////////////////////////////////////////////////////
/*
 *Simply sets a plan that's on a person's autoread list to be marked as read
 */
function update_read($dbh, $owner, $updated) {
mysql_query("UPDATE autofinger SET updated = '0' WHERE
owner = '$owner' and interest = '$updated'");
}

/////////////////////////////////////////////////////////////////////
/*
 *Set the time when the plan was updated
 */
function setUpdatedTime($idcookie)
{        mysql_query("UPDATE accounts SET changed = NOW() WHERE
        userid = $idcookie");
}

//////////////////////////////////////////////////////////////////////
/*
 *Get all of the plans that fall between two letters (usually the same one)
 */

function get_letters($dbh, $first_letter, $second_letter,$idcookie)
{
if (!$idcookie) {$guest="AND webview=1";}

$my_result = mysql_query("Select userid,username From accounts where
 username > '$first_letter' and username < '$second_letter' $guest ORDER BY username");

    while($new_row = mysql_fetch_row($my_result)) {
      $all[] = $new_row;
    }
return $all;
}

///////////////////////////////////////////////////////////////////////////
/*
 *Notes when a person logs in
 */

function setLogin($dbh, $idcookie)
{
mysql_query("UPDATE accounts SET login = NOW() WHERE userid = $idcookie");
}
///////////////////////////////////////////////////////////////////////////
/*
 *Marks when a person reads a plan
 */
function setReadTime($dbh, $idcookie,$interest)
{
mysql_query("UPDATE autofinger SET readtime = NOW() WHERE owner =
$idcookie AND interest = $interest");
}


///////////////////////////////////////////////////////////////////////////
/*
*Handles the cleaning up of a plan, such as allowing only certain html links in
*/
function cleanText($plan)
{


      $plan = htmlspecialchars($plan);//take out html

	//fix the dollar sign error- by josh
	//$plan = preg_replace("(\|(\w\s)*)\$

      $plan = preg_replace("/\n/s","<br>", $plan);
      $plan = preg_replace("/\&lt\;hr\&gt\;/si", "<hr><p class=\"sub\">", $plan);
      $plan = preg_replace("/\&lt\;b\&gt\;(.*?)\&lt\;\/b\&gt\;/si", "<b>\\1</b>",
                           $plan);//allow stuff in the bold tag back in
      $plan = preg_replace("/\&lt\;tt\&gt\;(.*?)\&lt\;\/tt\&gt\;/si", "<tt>\\1</tt>",
                           $plan);
      $plan = preg_replace("/\&lt\;pre\&gt\;(.*?)\&lt\;\/pre\&gt\;/si", "<pre>\\1</pre>",
                           $plan);
      $plan = preg_replace("/\&lt\;i\&gt\;(.*?)\&lt\;\/i\&gt\;/si", "<i>\\1</i>",
                           $plan);//allow stuff in the italics tag back in
      $plan = preg_replace("/\&lt\;u\&gt\;(.*?)\&lt\;\/u\&gt\;/si", "<u>\\1</u>",
                           $plan);//allow stuff in the underline tag back in
      $plan = preg_replace("/\&lt\;a.+href=.&quot\;(.+).&quot\;&gt\;(.+)&lt\;\/a&gt\;/si", "<a href=\"\\1\" class=\"onplan\">\\2</a>",$plan);


//$plan = preg_replace("/\&lt\;a.href=.&quot\;(.+).&quot\;/si", "EEE",$plan);

      $somearray = preg_match_all("/.*?\[(.*?)\].*?/s", $plan, $mymatches);//get an array of everything in brackets

      $matchcount = count($mymatches[1]);
      for ($o=0; $o<$matchcount; $o++)//do a loop to test whether everything in brackets is a valid user or not
        {
          $mycheck=$mymatches[1][$o];//get the current thing being tested

          if (!$checked[$mycheck])//make sure current thing being checked has not already been checked
            {
//check for plan with username
              if ($item = get_item($mydbh,"username","accounts","username", $mycheck))//see if is a valid user, if so also gets username
                {
                  $plan = preg_replace("/\[$mycheck\]/s", "[<a href=\"read.php?searchname=$item\" class=\"planlove\">$mycheck</a>]", $plan);//change all occurences of person on plan
                }
              else
                {
                              if ($item = get_item($mydbh,"messageid","subboard","messageid", $mycheck))
                {
                  $plan = preg_replace("/\[$mycheck\]/s", "[<a href=\"board_messages.php?messagenum=$item#$item\" class=\"boardlink\">$mycheck</a>]", $plan);
                }
                               if ($mycheck=="dnew")
                {
		  $plan = preg_replace("/\[dnew\]/s", "<b>" . date("F j, Y, l H:i") . "</b>",$plan);
                }
				if ($mycheck=="date")	
		{
		  $plan = preg_replace("/\[date\]/s", "<b>" . date("l F j, Y. g:i A") . "</b>",$plan);
		}		
                
                  if (strrpos($mycheck,":"))
                    {

                                    if (strrpos($mycheck,"|"))
                    {

                     preg_match("/(.+?)\|(.+)/si",$mycheck,$love_replace);


                     $plan=preg_replace("/\[" . preg_quote($mycheck,"/") . "\]/si", "<a href=\"$love_replace[1]\" class=\"onplan\">$love_replace[2]</a>",$plan);
                    }
                    else {
                   
                    $plan= preg_replace("/\[" . preg_quote($mycheck,"/") . "\]/si","<a href=\"$mycheck\" class=\"onplan\">$mycheck</a>",$plan);
  
                         }
                  }


                }
              $checked[$mymatches[1][$o]]=1;//mark checked values as checked, so don't have to check again
            }//if (!$checked[$mycheck])

        }//for ($o=0; $mymatches[1][$o]; $o++)
$plan=trim($plan);
return $plan;
}


///////////////////////////////////////////////////////////////////////////
/*
 *Simple beginning to guest display
 */
function gdisp_begin($dbh)
{

if (!$myprivl == 2 or !$myprivl == 3)
 {$myprivl = 1;}

?>
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <meta name="robots" content="noindex,nofollow" />
<title>Plans 2.2</title>
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
    $sql = "select count(*) as n from secrets where display = 'yes' and secrets.date > '$last'";
    $count = mysql_query($sql);
    $count_row = mysql_fetch_array($count);
    $count = $count_row['n'];   
    return $count;
}


?>
