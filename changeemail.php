<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'email', PLANSVNAME . ' - Change Email', 'changeemail.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} else {
    populate_page($thispage, $dbh, $idcookie);

    $changed = isset($_POST['changed'])?$_POST['changed']:false;
    $checknumb = isset($_POST['checknumb'])?$_POST['checknumb']:false;
    if ($changed && ($checknumb != User::id())) {
        $denied = new AlertText('Checknumbers do not match.', 'Error', true);
        $thispage->append($denied);
        interface_disp_page($thispage);
        db_disconnect($dbh);
        exit(0);
    }
    if ($changed == 'email') {
        $newemail = $_POST['email'];
        $user = User::get();
        if (isset($_POST['password']) && password_verify($_POST['password'],$user->password)) {
	    if ($newemail == "" || filter_var($newemail, FILTER_VALIDATE_EMAIL) !== false) {
            if (User::setEmail($newemail)) {
	            $success = new InfoText("Your email address has been updated!",'Success');
	            $thispage->append($success);
            } else {
                $msg = new AlertText('An unexpected error occurred.','Unknown Error');
                $thispage->append($msg);
            }
	    } else {
	        $msg = new AlertText('Not a valid email address.','Invalid Email', true);
	        $thispage->append($msg);
	    }
	    } else {
	        $msg = new AlertText('Your password was not entered correctly. Please try again.', 'Incorrect password', true);
	        $thispage->append($msg);
	    }
    }    
    $email = isset($newemail) ? $newemail : User::getEmail();

    $heading = new HeadingText('Change Permanent Email Address', 2);
    $thispage->append($heading);
    $about = new InfoText('This is your permanent email address. If you lose or forget your password, you will be able to have a new password sent to you. <br /> We will not use your email address for any other purpose. If you do not wish to provide your email address, leave the form blank.');

    $thispage->append($about);

    if (stristr($email, "@grinnell.edu")) {
        $about = new InfoText("Your email address ends with @grinnell.edu. We recommend using an email address that you will retain access to if and when you leave the College.");
        $thispage->append($about);
    }

    $emailform = new Form('emailform', true);
    $thispage->append($emailform);
    $eml = new TextInput('email', $email);
    $eml->title = 'Email address:';
    $emailform->append($eml);
    $passwd = new PasswordInput('password');
    $passwd->title = 'Current password:';
    $emailform->append($passwd);
    $changed = new HiddenInput('changed', 'email');
    $emailform->append($changed);
    $checknumb = new HiddenInput('checknumb', User::id());
    $emailform->append($checknumb);
    $sub = new SubmitInput('Set Permanent Email');
    $emailform->append($sub);    
    
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
