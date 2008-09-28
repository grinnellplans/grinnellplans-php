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
		echo '<img src="img/logo.jpg">';
	}
?>
		</td>
	  </tr>
	  <tr class="boxes">
		<td colspan=2 align=center class="boxes">
			<form name="post" action="login.php" method="POST">
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
		<form action="login.php" method="POST"> <!--gimmick to make the buttons display at the same height-->
		<td align=left width="50%">
			<input type="hidden" value="1" name="guest">
			<input type="submit" value="Guest">
			</form></td>
	  </tr>
	  <tr>
		<td align=center colspan=2>
		<font face=verdana>
			<p><?php
	echo $show_form
?></p>
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
</body>
</html>