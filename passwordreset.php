<?php
require_once ('Plans.php');
new SessionBroker();
require_once ("functions-main.php");
require_once ("syntax-classes.php");
$idcookie = User::id();
$dbh = db_connect();
$page = new PlansPage('Utilities', 'passwordreset', PLANSVNAME . ' - Password Reset', 'passwordreset.php');
if (User::logged_in()) {
    populate_page($page, $dbh, $idcookie);
} else {
    populate_guest_page($page);
}
$heading = new HeadingText('Password Reset', 1);
$page->append($heading);

if (User::logged_in()) {
    $msg = new AlertText('You are already logged in. You can change your password from the <a href="/changepassword.php">Change Password</a> page.','Already logged in');
    $page->append($msg);
} else if (isset($_POST['u']) && isset($_POST['email'])) {
    if(send_reset_email($_POST['u'], $_POST['email'])) {
        $msg = new InfoText("Check your email for a password reset link.", "Check email");
        $page->append($msg);
    } else {
        $msg = new AlertText("Error: The email address you provided does not match our records, or something else went wrong. ".'Please contact <a href="mailto:'.ADMIN_ADDRESS.'">'.ADMIN_ADDRESS.'</a> for assistance.',"Email address mismatch");
        $page->append($msg);
        $page->append(reset_step1());
    }
} else if (isset($_REQUEST['u']) && isset($_REQUEST['e']) && isset($_REQUEST['h'])) {
    if ((User::getPasswordResetHash($_REQUEST['u'],$_REQUEST['e']) == $_REQUEST['h']) && ($_REQUEST['e'] > time())) {
        if (isset($_POST['password1']) && isset($_POST['password2'])) {
            if (($_POST['password1'] == $_POST['password2']) && (strlen($_POST['password1']) >= 4)) {
                if (User::resetPassword($_REQUEST['u'],$_REQUEST['e'],$_REQUEST['h'],$_POST['password1'])) {
                    $msg = new InfoText('Your password has been changed. Please <a href="/index.php">log in!</a>!','Reset successful');
                    $page->append($msg);
                } else {
                    $msg = new AlertText('Sorry, an unknown error occured. Please try again.','Unknown error');
                    $page->append(msg);
                    $page->append(reset_step2());
                }

            } else if ($_POST['password1'] != $_POST['password2']) {
                //Password mismatch
                $msg = new AlertText("Error: The passwords you supplied did not match. Please try again.","Password mismatch");
                $page->append($msg);
                $page->append(reset_step2());
            } else {
                //Length requirement not met
                $msg = new AlertText("Error: Passwords must be at least four characters long.","Password too short");
                $page->append($msg);
                $page->append(reset_step2());
            }
        } else {
            $page->append(reset_step2());
        }
    } else { // Wrong hash
        $msg = new AlertText("Error: This password reset link may have already been used, or it may have expired. Please request another.", "Invalid link");
        $page->append($msg);
        $page->append(reset_step1());
    }
} else {
    $page->append(reset_step1());
}
interface_disp_page($page);
db_disconnect($dbh);

function reset_step1() {
    $form = new Form('reset_step1', true);
    $form->method = 'POST';
    $message = new InfoText('If you have lost your GrinnellPlans password, you may reset it by supplying your username and email address.', 'Reset your password');
    $form->append($message);
    $item = new TextInput('u', isset($_REQUEST['u'])?$_REQUEST['u']:null);
    $item->title = 'Username:';
    $form->append($item);
	$item = new TextInput('email', isset($_REQUEST['email'])?$_REQUEST['email']:null);
	$item->title = 'Email:';
	$form->append($item);
    $item = new SubmitInput('Send reset email');
    $form->append($item);
    return $form;
}

function reset_step2() {
    $form = new Form('reset_step2', true);
    $form->method = 'POST';
    $msg = new InfoText('Please choose a new password for your GrinnellPlans account. Passwords must be four characters long or more.','Choose new password');
    $form->append($msg);
    $username = new TextInput('u',$_REQUEST['u']);
    $username->title = 'Username:';
    $username->readonly = true;
    $form->append($username);
    $pw1 = new PasswordInput('password1');
    $pw1->title = "New Password:";
    $form->append($pw1);
    $pw2 = new PasswordInput('password2');
    $pw2->title = "Verify:";
    $form->append($pw2);
    $submit = new SubmitInput('Change Password');
    $form->append($submit);
    $e = new HiddenInput('e',$_REQUEST['e']);
    $form->append($e);
    $h = new HiddenInput('h',$_REQUEST['h']);
    $form->append($h);
    return $form;
}

function send_reset_email($username, $email) {
    $user = User::get($username);
    if (($user === false) || ($user->username != $username) || ($user->email != $email)) return false;
    $expires = time() + 24*60*60;
    $hash = User::getPasswordResetHash($user->username,$expires,$user);
    if (!$hash) return false;
    $url = 'http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')?'s':'').'://'.$_SERVER['SERVER_NAME'].'/passwordreset.php?u='.$user->username.'&e='.$expires.'&h='.$hash;
    $emailbody = "Dear [$user->username],\n\n";
    $emailbody.= "We received a request at www.grinnellplans.com to reset your Plans password.\n";
    $emailbody.= "To confirm this request and reset your GrinnellPlans password, please click the link below: \n\n";
    $emailbody.= $url."\n\n";
    $emailbody.= "If you are still having trouble accessing your GrinnellPlans account, reply to this email, and tell us what's going on.\n";
    $emailbody.= "If you did not request a password reset, you may safely ignore this email. Your password will not be changed.\n\n";
    $emailbody.= "Thanks for your continued interest in Plans!\nThe Plans Admins";
    return send_mail($email,"GrinnellPlans password reset",$emailbody);
}
?>
