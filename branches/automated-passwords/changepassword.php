<?php
require_once('Plans.php');
require('functions-main.php');
require('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'password', PLANSVNAME . ' - Change Password', 'changepassword.php');
if (!User::logged_in()) {
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
	$thispage->append($denied);
} else {
	populate_page($thispage, $dbh, $idcookie);

	$account = Doctrine::getTable('Accounts')->find($idcookie);

	$real_pass = $account->guest_password;
	$username = $account->username;
	$email = $account->email;
	if ($changed && ($checknumb != $idcookie)) {
		$denied = new AlertText('Checknumbers do not match.', 'Error', true);
		$thispage->append($denied);
		interface_disp_page($thispage);
		db_disconnect($dbh);
		exit(0);
	}
	if ($changed && ($changed != "email") && ($mypassword != $mypassword2)) {
		$denied = new AlertText('Passwords do not match.', 'Error', true);
		$thispage->append($denied);
		interface_disp_page($thispage);
		db_disconnect($dbh);
		exit(0);
	}
	if ($changed == 'pass') {
		if (!(strstr($mypassword, "\"") or strstr($mypassword, "\'"))) {
			if (strlen($mypassword) > 3) {
				$crpassword = User::hashPassword($mypassword);
				$account->password = $crpassword;
				$account->save();
				$success = new InfoText("Your password has been changed!", 'Success');
				$thispage->append($success);

			} else {
				$denied = new AlertText('Could not change password. Your password must be 4 or more characters.', 'Bad Password', true);
				$thispage->append($denied);
			}
		} else {
				$denied = new AlertText('Illegal character in password. Please do not use " or \'.', 'Bad Password', true);
				$thispage->append($denied);
		}
	}
	if ($changed == 'guest_pass') {
		$guest_password = $_POST['guest_password'];
		$account->guest_password = $guest_password;
		$account->save();
		$real_pass = $guest_password;
	}
	if ($changed == 'email')
	{
		$newemail = $_POST['newemail'];
		if (isValidEmail($newemail)) {
			$account->email = $newemail;
			$account->save();
			$email = $newemail;
		} else {
			$denied = new AlertText('That is not a valid email address', 'Invalid email', true);
			$thispage->append($denied);
		}
	}


	$heading = new HeadingText('Change Login Password', 2);
	$thispage->append($heading);

	$passwordform = new Form('passwordform', true);
	$thispage->append($passwordform);
	$pw1 = new PasswordInput('mypassword');
	$pw1->title = 'New Password:';
	$passwordform->append($pw1);
	$pw2 = new PasswordInput('mypassword2');
	$pw2->title = 'Confirm:';
	$passwordform->append($pw2);
	$changed = new HiddenInput('changed', 'pass');
	$passwordform->append($changed);
	$checknumb = new HiddenInput('checknumb', $idcookie);
	$passwordform->append($checknumb);
	$sub = new SubmitInput('Change Password');
	$passwordform->append($sub);
	
	$heading = new HeadingText('Password Reset E-mail Account',2);
	$thispage->append($heading);
	$about = new InfoText("Your password reset email account is currently $email");
	$thispage->append($about);
	$email = new Form("emailform", true);
	$newemail = new TextInput('newemail');
	$newemail->title = 'New E-mail:';
	$email->append($newemail);
	$changed = new HiddenInput('changed', 'email');
	$email->append($changed);
	$checknumb = new HiddenInput('checknumb', $idcookie);
	$email->append($checknumb);
	$sub = new SubmitInput('Change E-mail Address');
	$email->append($sub);	
	$thispage->append($email);	

	$heading = new HeadingText('Set Guest Password', 2);
	$thispage->append($heading);
	$about = new InfoText('This is a password you can use to allow non-Plans users to read your plan.  They will not be able to edit your plan or use any other plans features. <br />
This feature is intended to allow people to share their Plans with a small number of personal friends. 
At any time, you may change this password to prevent people from accessing your plan using the old guest password.');
	$thispage->append($about);

	if ($real_pass) {
		$about = new InfoText('You may give this link out to anyone who you would like to be able to read you plan and ask them to bookmark it:');
		$thispage->append($about);
		$link = new Hyperlink('guestpass', true, "http://www.grinnellplans.com/read.php?searchname=$username&amp;guest-pass=$real_pass");
		$thispage->append($link);
	} else {
		$about = new InfoText('Currently, your plan is completely private since you do not have a guest password set up.');
		$thispage->append($about);
	}

	$passwordform = new Form('guestpasswordform', true);
	$thispage->append($passwordform);
	$pw1 = new TextInput('guest_password', $real_pass);
	$passwordform->append($pw1);
	$changed = new HiddenInput('changed', 'guest_pass');
	$passwordform->append($changed);
	$checknumb = new HiddenInput('checknumb', $idcookie);
	$passwordform->append($checknumb);
	$sub = new SubmitInput('Set Guest Password');
	$passwordform->append($sub);
	
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
