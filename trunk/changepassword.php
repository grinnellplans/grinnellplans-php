<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'password', PLANSVNAME . ' - Change Password', 'changepassword.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} else {
    populate_page($thispage, $dbh, $idcookie);

    $real_pass = get_item($dbh, "guest_password", "accounts", "userid", $idcookie);
    $username = get_item($dbh, "username", "accounts", "userid", $idcookie);
    $email = get_item($dbh, "email", "accounts", "userid", $idcookie);
    $changed = $_POST['changed'];
    $checknumb = $_POST['checknumb'];
    if ($changed && ($checknumb != $idcookie)) {
        $denied = new AlertText('Checknumbers do not match.', 'Error', true);
        $thispage->append($denied);
        interface_disp_page($thispage);
        db_disconnect($dbh);
        exit(0);
    }
    if ($changed && ($mypassword != $mypassword2)) {
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
                set_item($dbh, "accounts", "password", $crpassword, "userid", $idcookie); //set the password
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
        set_item($dbh, "accounts", "guest_password", $guest_password, "userid", $idcookie);
        $real_pass = $guest_password;
    }
    if ($changed == 'email') {
        $newemail = $_POST['email'];
	if ($newemail == "" || filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
	    set_item($dbh, "accounts", "email", $newemail, "userid", $idcookie);
	    $email = $newemail;
	    $success = new InfoText("Your email address has been updated!",'Success');
	    $thispage->append($success);
	} else {
	    $msg = new AlertText('Not a valid email address.','Invalid Email', true);
	    $thispage->append($msg);
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

    $heading = new HeadingText('Change Permanent Email Address', 2);
    $thispage->append($heading);
    $about = new InfoText('This is your permanent email address. If you lose or forget your password, you will be able to have a new password sent to you. <br /> We will not use your email address for any other purpose. If you do not wish to provide your email address, leave the form blank.');

    $thispage->append($about);

    if (stristr($email, "@grinnell.edu") && (get_item($dbh, "user_type", "accounts", "userid", $idcookie)=="student")) {
        $about = new InfoText("Your email address ends with @grinnell.edu. We recommend using an email address that you will retain access to after you leave the College.");
        $thispage->append($about);
    }

    $emailform = new Form('emailform', true);
    $thispage->append($emailform);
    $eml = new TextInput('email', $email);
    $emailform->append($eml);
    $changed = new HiddenInput('changed', 'email');
    $emailform->append($changed);
    $checknumb = new HiddenInput('checknumb', $idcookie);
    $emailform->append($checknumb);
    $sub = new SubmitInput('Set Permanent Email');
    $emailform->append($sub);    
    
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
