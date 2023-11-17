<?php
require_once ('Plans.php');
if (isset($_GET['logout'])) {
    User::logout();
    $msg = 'You have been successfully logged out.';
} else if (User::logged_in()) {
    Redirect('home.php');
} else if (isset($_POST['submit'])) {
    if (isset($_POST['guest'])) {
        Redirect('home.php');
    } else {
        $user = User::login($_POST['username'], $_POST['password']);
        if (!$user) {
            $msg = 'Invalid username or password.</a>';
        } else {
            $user->JsStatus->status = $_POST['js_test_value'];
            $user->save();
            Redirect('home.php');
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">   
<html dir="ltr">
<head>
	<title>GrinnellPlans</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		img {
			max-width: 100%;
			height: auto;
		}
	</STYLE>
<script src="https://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
google.load("jquery", "1.3.2");
google.load("jqueryui", "1.7.2");
</script>
<script src="js/chroma-hash.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$("#username").focus();
	$("input:password").chromaHash({bars: 3, salt: "5bf999ac93430ed303cd0b56f976e289", minimum: 4});
});
</script>
</head>
<body bgcolor="#ffffff">       
	<div class="left"><br><br>
	  <table cellpadding=0 width="100%">
	  <tr>
		<td colspan=2 align=center>
			<img src="images/logo.png">
		</td>
	  </tr>
	  <tr class="boxes">
		<td colspan=2 align=center class="boxes">
			<form name="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="boxes">
				Username: <input type="text" id="username" name="username"><br>
				Password: <input type="password" id="password" name="password"><br>
			</div>
		</td>
	  </tr>
	  <tr valign=top>
		<td align=right width="50%">
			<input type="submit" name="submit" value="Login">
			<input type="hidden" value="off" name="js_test_value">  
<script>
<!--
document.post.js_test_value.value = "on";
-->
</script>  
			</form></td>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> <!--gimmick to make the buttons display at the same height-->
		<td align=left width="50%">
			<input type="hidden" value="1" name="guest">
			<input type="submit" name="submit" value="Guest">
			</form></td>
	  </tr>
	  <tr>
		<td align=center colspan=2>
<?php
if (isset($msg)) {
?>
		<font face=verdana>
		<p><?php echo $msg; ?>
		</p>
		<?php
}
?>
        <br><a href="passwordreset.php">Forgot your password?</a>
        <br>
		<br>Need a plan? <a href="register.php">Register</a> if you have an @grinnell.edu email address.
<br />  
<span style="font-size: 1em"> 
If you no longer have access to your @grinnell.edu email address, you may reactivate it <a href="https://alumni.grinnell.edu/college-resources/alumni-email">here</a>. <br />If you're still having problems registering on plans, <a href="mailto:grinnellplans@gmail.com">email us</a>.
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
