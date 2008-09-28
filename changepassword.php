<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect(); //connect to database
$idcookie = User::id();
if (!User::logged_in()) {
	gdisp_begin($dbh); //begin guest display
	echo ("You are not allowed to edit as a guest."); //tell guest they can't use page
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin valid user display
	$real_pass = get_item($mydbh, "guest_password", "accounts", "userid", $idcookie);
	$username = get_item($mydbh, "username", "accounts", "userid", $idcookie);
	if ($changed && ($checknumb != $idcookie)) {
		echo "Error: Checknumbers do not match.";
		exit(0);
	}
	if ($changed && ($mypassword != $mypassword2)) {
		echo "Error: Passwords do not match.\n";
		exit(0);
	}
	if ($changed == 'pass') {
		if (!(strstr($mypassword, "\"") or strstr($mypassword, "\'"))) {
			if (strlen($mypassword) > 3) {
				$crpassword = crypt($mypassword, "ab"); //encrypt the password,
				set_item($dbh, "accounts", "password", $crpassword, "userid", $idcookie); //set the password
				echo "Password changed to <b>" . $mypassword . "</b>."; //confirm changed password
				
			} else {
				echo "Could not change password. Your password must be 4 or more characters.";
			}
		} else {
			echo "Illegal character in password. Please do not use \" or '.";
		}
	}
	if ($changed == 'guest_pass') {
		$guest_password = $_POST['guest_password'];
		set_item($mydbh, "accounts", "guest_password", $guest_password, "userid", $idcookie);
		$real_pass = $guest_password;
	}
?>
		<center><h2>Change Login Password</h2>
		<form method="POST" action="">
<table>
	<tr><td>New Password:</td><td><input type="password" name="mypassword"></td></tr>
		<tr><td>Confirm: </td><td><input type="password" name="mypassword2"></td></tr></table> <br />
		<input type="hidden" name="myprivl" value="<?php
	echo $myprivl; ?>">
		<input type="hidden" name="changed" value="pass">
		<input type="hidden" name="checknumb" value="<?php
	echo $idcookie; ?>">
		<input type="submit" value="Change Password">
		</form>
		</center>
		<center><h2>Set Guest Password</h2>
		<p>
		This is a password you can use to allow non-Plans users to read your plan.  They will not be able to edit your plan or use any other plans features. <br />
This feature is intended to allow people to share their Plans with a small number of personal friends. 
At any time, you may change this password to prevent people from accessing your plan using the old guest password.
<br />
<?php
	if ($real_pass) { ?>
You may give this link out to anyone who you would like to be able to read you plan and ask them to bookmark it: <br />
<a href="http://www.grinnellplans.com/read.php?searchname=<?php
		echo $username
?>&amp;guest-pass=<?php
		echo $real_pass
?>">
http://www.grinnellplans.com/read.php?searchname=<?php
		echo $username
?>&amp;guest-pass=<?php
		echo $real_pass
?>
</a> <br />
<?php
	} else { ?>
<b>Currently, your plan is completely private since you do not have a guest password set up.</b>
<?php
	} ?>
		</p>
		<form method="POST" action="">
		<input type="text" name="guest_password" value="<?php
	echo $real_pass
?>">
		<input type="hidden" name="myprivl" value="<?php
	echo $myprivl; ?>">
		<input type="hidden" name="changed" value="guest_pass">
		<input type="hidden" name="checknumb" value="<?php
	echo $idcookie; ?>">
		<input type="submit" value="Set Guest Password">
		</form>
		</center>
		<?php
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //end valid user display
	
}
db_disconnect($dbh);
?>
