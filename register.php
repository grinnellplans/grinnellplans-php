<?php
require_once ('Plans.php');
new SessionBroker();
require ("functions-main.php");
require ("functions-kommand.php");
require ("functions-email.php");
require ("syntax-classes.php");
$idcookie = User::id();
$userid = $idcookie;
$dbh = db_connect();
$admin_email = ADMIN_ADDRESS;
$domain = "grinnell.edu";
$thispage = new PlansPage('Utilities', 'register', PLANSVNAME . ' - Registration', 'register.php');
if (User::logged_in()) {
    populate_page($thispage, $dbh, $idcookie);
} else {
    populate_guest_page($thispage);
}
$heading = new HeadingText('Plan Registration', 1);
$thispage->append($heading);
if ($_GET['submitted']) {
    $username = $_GET['username'];
    $match = array();
    if (preg_match("/(.*)@$domain/", $username, $match)) {
        $username = $match[1];
    }
    $username = preg_replace('/[^a-z0-9]/', '', strtolower($username));
    $year = $_GET['gradyear'];
    $year = preg_replace("/[^0-9]/", '', $year);
    $type = $_GET['type'];
    if ($type == "other") {
        $type = $_GET['other'];
    }
    if ($username == '' || get_item($dbh, 'username', 'accounts', 'username', $username)) {
        $thispage->append(show_username_taken($username));
    } else {
        $token = make_token();
        $data = array('username' => $username, 'year' => $year, 'type' => $type);
        $storable = serialize($data);
        $email = $username . '@' . $domain;
        mysqli_query($dbh,"insert into tentative_accounts set session = '$storable', token = '$token', created = now()");
        $message = "Click the following link to activate your Plan:\n" . "www.grinnellplans.com/register.php?token=$token\n\n" . "The link will expire in 24 hours.";
        if (send_mail($email, "Activate your new plan.", $message)) {
        $message = new InfoText("An email has been sent to $email with a link to activate your Plan.  You will probably receive it right away, but if you don't get it within a few hours, <a href=\"mailto:$admin_email\">Bug us</a>.", 'Email Sent');
        } else {
        $message = new AlertText("We were not able to send you an activation email, possibly because your email address is not accepting messages at this time. Please contact <a href=\"mailto:$admin_email\">$admin_email</a> for assistance.",'Activation email could not be sent');
        }
        $thispage->append($message);
    }
} else if ($_GET['token']) {
    $session = get_item($dbh, 'session', 'tentative_accounts', 'token', $token);
    if (!$session) {
        $message = new AlertText('That doesn\'t seem to be a valid or unexpired token, please try again or <a href="mailto:grinnellplans@gmail.com">Email</a> us.', 'Token not recognized');
        $thispage->append($message);
        $thispage->append(show_form());
    } else {
        $data = unserialize($session);
        $username = $data['username'];
        $type = $data['type'];
        $year = $data['year'];
        $email = $username . '@' . $domain;
        if (get_item($dbh, 'username', 'accounts', 'username', $username)) {
            $message = new AlertText("A plan with the username $username already exists, meaning this token has been used.  If you are the owner of that email, your password was given to you when you first clicked the link.  If you've lost the password, or for anything else, <a href=\"mailto:$admin_email\">Email us</a>.", 'Plan exists');
            $thispage->append($message);
            $thispage->append(show_form());
        } else {
            $results = insert_user($username, '', $year, $email, $type);
            $password = $results[0];
            $message = new InfoText("Your account has been created!  Your username is $username and your initial password is $password." . '  Go <a href="http://www.grinnellplans.com/">Here</a> to test them out.', 'Plan Created');
            $thispage->append($message);
            $message = "A new plan has been created with \nusername:  $username\nGrad Year: $year\n$username self-identifies as $type.";
            send_mail($admin_email, "Plan Created: $username", $message);
            $message = "Your account has been created!  Your username is $username and your initial password is $password. Go to http://www.grinnellplans.com/ to get started.\n";
            send_mail($email, "Plan Created", $message);
        }
    }
} else {
    $thispage->append(show_form());
}
interface_disp_page($thispage);
db_disconnect($dbh);
/*
?>
<script>
<!--
document.getElementById('year').style.display = 'none';
document.getElementById('other').style.display = 'none';
recount_chars();


function recount_chars() {
document.getElementById("char_count").innerHTML = document.signup.other.value.length;
}

function toggle(item, box) {
if ( document.signup.type[box].checked == true) {
document.getElementById(item).style.display = 'inline';
} else {
document.getElementById(item).style.display = 'none';
}
}
-->
</script>
<?php
*/
function show_username_taken($username) {
    return new AlertText(" Oh nO!, the username '$username' is already taken.  Please <a href=" . '"mailto:grinnellplans@gmail.com"' . ">Email</a> us and we'll make your account by hand.", 'Username taken');
}
function show_form() {
    $form = new Form('signup', true);
    $form->method = 'GET';
    $message = new InfoText('If you have an @grinnell.edu email address for yourself or a student group, you may use this page to register a Plan for that username.<br />
	<b>If you are an alum</b>, please <a href="mailto:grinnellplans@gmail.com">Send us</a> your alumni.grinnell.edu email address and we will contact you through it with a username and password.  Please include your year of graduation, if any.<br />
	If you are somebody else, or have questions, <a href="mailto:grinnellplans@gmail.com">Ask us</a>, and we\'ll see what we can do.', 'Register your plan');
    $form->append($message);
    $instruct = new InfoText('Enter your Grinnell username below (this is the part of your email address that comes before the \'@\', and click Register.  This will send you an email with a link that will complete your account creation.', 'Email needed');
    $form->append($instruct);
    $item = new TextInput('username', null);
    $item->title = 'Grinnell email username:';
    $form->append($item);
    /*
    $acct_type = new WidgetList('accounttype', true);
    $acct_type->title = 'What is your relation to Grinnell?';
    $form->append($acct_type);
    */
    $group = new FormItemSet('studenttype', true);
    $form->append($group);
    $item = new RadioInput('type', 'student');
    $item->description = 'Student';
    $group->append($item);
    /*<input type="radio" name="type" value="student" onClick ="toggle('year', 0);toggle('other', 4);">*/
    $item = new TextInput('gradyear', null);
    $item->description = 'Grad Year';
    $group->append($item);
    /*<span id="year"> Grad Year: <input type="text" name="gradyear"> </span>*/
    $item = new RadioInput('type', 'staff');
    $item->description = 'Staff';
    $form->append($item);
    $item = new RadioInput('type', 'group');
    $item->description = 'Group';
    $form->append($item);
    $item = new RadioInput('type', 'faculty');
    $item->description = 'Faculty';
    $form->append($item);
    $item = new SubmitInput('Register');
    $form->append($item);
    $item = new HiddenInput('submitted', 1);
    $form->append($item);
    return $form;
}
function make_token() {
    $length = 8;
    $token = '';
    for ($i = 0;$i < $length;$i++) {
        $next_int = rand(0, 64);
        if ($next_int < 10) {
            $next = chr($next_int + 48);
        } else if ($next_int < 36) {
            $next = chr($next_int + 55);
        } else if ($next_int < 62) {
            $next = chr($next_int + 61);
        } else if ($next_int == 62) {
            $next = '-';
        } else {
            $next = '_';
        }
        $token.= $next;
    }
    return $token;
}
?>
