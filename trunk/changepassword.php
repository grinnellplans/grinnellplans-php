<?php
require_once ('Plans.php');
require ('functions-main.php');
require ('syntax-classes.php');
$dbh = db_connect();
$idcookie = User::id();
$changed = isset($_REQUEST['changed'])?$_REQUEST['changed']:false;
$checknumb = isset($_REQUEST['checknumb'])?$_REQUEST['checknumb']:false;
$thispage = new PlansPage('Preferences', 'password', PLANSVNAME . ' - Change Password', 'changepassword.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} else {
    populate_page($thispage, $dbh, $idcookie);

    $guest_pass = User::get()->guest_password;
    if ($changed && ($checknumb != User::id())) {
        $denied = new AlertText('Checknumbers do not match.', 'Error', true);
        $thispage->append($denied);
        interface_disp_page($thispage);
        db_disconnect($dbh);
        exit(0);
    }
    if ($changed == 'pass') {
        if ($_POST['mypassword'] == $_POST['mypassword2']) {
            if (strlen($_POST['mypassword']) > 3) {
                if (User::changePassword(User::get()->username,$_POST['mypassword'],$_POST['oldpassword'])) {
                    $success = new InfoText("Your password has been changed!", 'Success');
                    $thispage->append($success);
                } else {
                    $denied = new AlertText('Could not change password. Did you type your old password correctly?', 'Bad Password', true);
                    $thispage->append($denied);
                }
            } else {
                $denied = new AlertText('Could not change password. Your password must be 4 or more characters.', 'Bad Password', true);
                $thispage->append($denied);
            }
        } else {
            $denied = new AlertText('Could not change password. New passwords do not match.', 'Bad Password', true);
            $thispage->append($denied);
        }
    }
    if ($changed == 'guest_pass') {
        $new_guest_password = $_POST['guest_password'];
        $user = User::get();
        $user->guest_password = $new_guest_password;
        $user->save();
        $guest_pass = $new_guest_password;
    }
    $heading = new HeadingText('Change Login Password', 2);
    $thispage->append($heading);

    $passwordform = new Form('passwordform', true);
    $thispage->append($passwordform);
    $oldpw = new PasswordInput('oldpassword');
    $oldpw->title = 'Old password:';
    $passwordform->append($oldpw);
    $pw1 = new PasswordInput('mypassword');
    $pw1->title = 'New Password:';
    $passwordform->append($pw1);
    $pw2 = new PasswordInput('mypassword2');
    $pw2->title = 'Confirm:';
    $passwordform->append($pw2);
    $changed = new HiddenInput('changed', 'pass');
    $passwordform->append($changed);
    $checknumb = new HiddenInput('checknumb', User::id());
    $passwordform->append($checknumb);
    $sub = new SubmitInput('Change Password');
    $passwordform->append($sub);

    $heading = new HeadingText('Set Guest Password', 2);
    $thispage->append($heading);
    $about = new InfoText('This is a password you can use to allow non-Plans users to read your plan.  They will not be able to edit your plan or use any other plans features. <br />
This feature is intended to allow people to share their Plans with a small number of personal friends. 
At any time, you may change this password to prevent people from accessing your plan using the old guest password.');
    $thispage->append($about);

    if ($guest_pass) {
        $about = new InfoText('You may give this link out to anyone who you would like to be able to read you plan and ask them to bookmark it:');
        $thispage->append($about);
        $link = new Hyperlink('guestpass', true, "http://www.grinnellplans.com/read.php?searchname=".User::name()."&amp;guest-pass=$guest_pass");
        $thispage->append($link);
    } else {
        $about = new InfoText('Currently, your plan is completely private since you do not have a guest password set up.');
        $thispage->append($about);
    }

    $passwordform = new Form('guestpasswordform', true);
    $thispage->append($passwordform);
    $pw1 = new TextInput('guest_password', $guest_pass);
    $passwordform->append($pw1);
    $changed = new HiddenInput('changed', 'guest_pass');
    $passwordform->append($changed);
    $checknumb = new HiddenInput('checknumb', User::id());
    $passwordform->append($checknumb);
    $sub = new SubmitInput('Set Guest Password');
    $passwordform->append($sub);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
