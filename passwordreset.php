<?php
require_once('Plans.php');
require('functions-main.php');
require('syntax-classes.php');
$dbh = db_connect();


if (isset($_POST['submit'])) {
	if(User::resetPassword($_POST['username'], $_POST['email'])) {
		$msg = "New password sent! Check your email, then <a href=\"index.php\">log in again</a>.";
	} else {
		$msg = "Unable to verify password reset e-mail address. Please contact <a href=\"mailto:grinnellplans@gmail.com\">grinnellplans@gmail.com</a> for assistance.";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">   
<html dir="ltr">
<head>
	<title>GrinnellPlans</title>
	<STYLE TYPE="text/css">
	<!--
	BODY { 
			font-family: verdana;
			}
	TD { 
			align: center;
			}
	.boxes { 
			font-family: courier; 
			}
	.buttons {
			}
	.graphic{
			position: relative;
			top: 50px;
			}
	.legalese {
			position: static; 
			text-align: justify;
			cellpadding: 3;
			font-size: 8pt;
			font-family: verdana;
			}
	-->
	</STYLE>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$("#username").focus();
});
</script>
</head>
<body bgcolor="#ffffff">       
	<div class="left"><br><br>
	  <table cellpadding=0 width="100%">
	  <tr>
		<td colspan=2 align=center>
		<a href="index.php"><img src="images/logo.png" style="border-style: none"></a>
		</td>
	  </tr>
	  <form name="post" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	  <tr class="boxes" align="center">
		<td colspan=2 class="boxes">		
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Username: <!-- gag! --> 
			<input type="text" id="username" name="username" />
		</td>
	   </tr>
	   <tr class="boxes" align="center">
		<td colspan=2 class="boxes">
			E-mail Address:
			<input type="text" id="email" name="email" />
		</td>
	  </tr>
	  <tr valign=top>
		<td align=center colspan=2>
			<input type="submit" name="submit" value="Reset">
		</td>
	  </tr>
	  </form>
	  <tr>
		<td align=center colspan=2>
<?php
	if (isset($msg)) {
?>
		<font face=verdana>
		<p><?=$msg?>
		</p>
		<?php
	}
		?>
		<br>
</span>
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
</body>
</html>
