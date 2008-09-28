<?php
	require_once("Plans.php");
require("functions-main.php");

if ($_GET['logout']) {
	$_SESSION['is_logged_in'] = 0;
	$_SESSION['userid'] = false;
	session_destroy();
}

$dbh=db_connect();
$auth = $_SESSION['is_logged_in'];
$myprivl = $_GET['myprivl'];

if ( $auth) {
	$username = $_SESSION['username'];
	$idcookie = $_SESSION['userid'];
} else {

	$username = $_POST['username'];
	$password = $_POST['password'];
	$guest = $_POST['guest'];


	if ($username) {


		if (isValidUser($dbh, $username)) {
			$orig_pass = $password;
			$password = crypt($password, "ab");
			$read_pass = get_item($dbh, "password", "accounts", "username",
			$username); //get password encrypted password in db

			//echo ($username . " " . $orig_pass . " " . $password . " " . $read_pass);
			if ($password == $read_pass) 

			{

				$idcookie = get_item($dbh, "userid", "accounts", "username", $username);
				setLogin($dbh, $idcookie);
				$_SESSION['is_logged_in'] = 1;
				$_SESSION['username'] = $username;
				$_SESSION['userid'] = $idcookie;
				$sql = "insert into js_status set userid = " . addslashes($idcookie) . ", status = '" . addslashes($_POST['js_test_value']) . "'";
				mysql_query($sql);

			}
		}
		if (! $_SESSION['is_logged_in']) {
			$show_form = "Invalid username or password.<br>";
		}
	}
	else {
		if (!$guest) {$show_form = " ";}
	}


}

//print_r($_SESSION);
//Visitor display - login form

//If there is a show_form comment to be placed at top of form, show the form. If no comment you want to add, just set to a space to have it show the form.

if ($show_form) 
{
    ?>
    
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">   
<html dir="ltr">
   <head>
   <title>GrinnellPlans</title>
   <link rel="stylesheet" href="index.css">
<script>
<!--
function js_test ()
{
    document.post.js_test_value.value = "on";
}
-->
</script> 
   </head>
   
       <body bgcolor="#ffffff" onLoad="self.focus();document.post.username.focus()">       
     
	<div class="left"><br><br>
	  <table cellpadding=0 width="100%">
	  <tr>
		<td colspan=2 align=center>
	<?php
if (isset($_GET['noimage'])) {
} else {
echo '<img src="logo.jpg">';
}
?>
		</td>
	  </tr>
	  <tr class="boxes">
		<td colspan=2 align=center class="boxes">
			<form name="post" action="index.php" method="POST">
			<div class="boxes">
				Username: <input type="text" name="username"><br>
				Password: <input type="password" name="password"><br>
			</div>
		</td>
	  </tr>
	  <tr valign=top>
		<td align=right width="50%">
			<input type="submit" value="Login">
			<input type="hidden" value="off" name="js_test_value">  
<script>
<!--
js_test();
-->
</script>  
			</form></td>
		<form action="index.php" method="POST"> <!--gimmick to make the buttons display at the same height-->
		<td align=left width="50%">
			<input type="hidden" value="1" name="guest">
			<input type="submit" value="Guest">
			</form></td>
	  </tr>
	  <tr>
		<td align=center colspan=2>
		<font face=verdana>
			<p><? echo $show_form ?></p>
		<br>
		<br>Need a plan? <a href="register.php">Register</a> if you have an @grinnell.edu email address.
<br />  
<span style="font-size:.7em"> 
Otherwise <a href="mailto:grinnellplans@gmail.com">email us</a>.
Alumni, please include your @alumni.grinnell.edu forwarding address (which you can get <a href="http://www.alumniconnections.com/olc/pub/GRN/permanentemail.html">here</a>) and grad year.
<br />
 
</span>
		<br>What is plans? <a href="documents/faq.html">GrinnellPlans FAQ</a>
		<br>Email <a href="mailto:grinnellplans@gmail.com">grinnellplans@gmail.com</a> with questions or concerns.
		</font>
		<br><br><br><br><font size="-1" face="verdana"><i>This site is not owned by, operated by, or officially  affiliated with Grinnell College, Grinnell, IA.</i>
		</td>
	  </tr>
	  </table>
	</div>
		<hr>
	<center>
	<p class="legalese">
Use of the GrinnellPlans service means you have accepted the <a href="http://www.grinnellplans.com/tos/">GrinnellPlans Terms of Service</a> agreement. If you do not accept and abide by this agreement, you may not use GrinnellPlans. This agreement is subject to change without notice, so you should periodically review the most up-to-date version.	
</p>


       <?
       }

//Part 3: Logged in as a user or guest
//At this point we've handled the loggin in part of the process and the processing should now
//handle what comes after the person is either an accepted
//user or logged in as a guest.
if ($_SESSION['is_logged_in'] or $guest) {
/*
echo "<br />";
print_r ($_SESSION);
echo "<br />";
echo "idcookie is $idcookie<br>";
echo "<br />" . $_SESSION['is_logged_in'];
echo "<br />" . $guest;
echo "<br />";
*/

	if ($_SESSION['is_logged_in']) {
		mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//send beginning display info          
	}

	if ($guest) {
		$dbh = db_connect();//sets up connection to database.
		gdisp_begin($dbh); //sends beginning part of guest display
		$my_result = mysql_query("Select system.motd From system"); //get the main plans message from the database
		$my_row = mysql_fetch_array($my_result); //get information from mysql query
	} else {
		$my_result = mysql_query("Select system.motd,accounts.spec_message From
				system,accounts where accounts.userid = '$idcookie'");//get the main plans messsage as well as the person's private message to be displayed
			$my_row = mysql_fetch_array($my_result); //get information from mysql query
		echo stripslashes(stripslashes($my_row[1]));//if logged in, show the private message

		echo '<pre>';
		echo '</pre>';

	}

	echo stripslashes(stripslashes($my_row[0])); //display the main Plans message

	if ($_SESSION['is_logged_in']) {
		mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl); //and send closing display data
	} else {
		gdisp_end();
	}//if guest send guest closing display data

}
db_disconnect($dbh);
?>
