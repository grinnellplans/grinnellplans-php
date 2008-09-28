<?php
	require_once("Plans.php");

require("functions-main.php");//load main functions
require("functions-kommand.php");//load main functions
$idcookie = $_SESSION['userid']; 
$userid = $idcookie;
$auth = $_SESSION['is_logged_in'];


$dbh = db_connect();//connect to database
$admin_email = "grinnellplans@gmail.com";

$myprivl=setpriv($myprivl, $HTTP_COOKIE_VARS["thepriv"]);

if ($auth) {
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);
} else { 
	gdisp_begin($dbh);
}
?>
<br />
<br />
<br />
<br />
<br />

<?php
if ($_GET['submitted'] ) {
	$username = $_GET['username'];
	$match = array();
	if (preg_match('/(.*)@grinnell.edu/', $username, $match )) {
		$username = $match[1];
	}
	$orig_username = $username;

	$username = preg_replace('/[^a-zA-Z+0-9]/', '', $username);
	$year = $_GET['gradyear'];
	$year = preg_replace("/[^0-9]/", '', $year);
	$type = $_GET['type'];
	if ($type == "other") {
		$type = $_GET['other'];
	}

	if ( $username == '' || get_item($dbh, 'username', 'accounts', 'username' , $username)) {
		show_username_taken($orig_username);
	} else {
		$token = make_token();
		$data = array('username' => $username, 'year' => $year, 'type' => $type);

		$storable = serialize($data);
		$email = $username . '@grinnell.edu';

		mysql_query("insert into tentative_accounts set session = '$storable', token = '$token', created = now()"); 

		$message= "Click the following link to activate your Plan:\n" . 
			"www.grinnellplans.com/register.php?token=$token\n\n" . 
			"The link will expire in 24 hours.";

		mail($email, "Activate your new plan.", $message,
				"From:$admin_email\nReply-to:$admin_email");
		echo "An email has been sent to $email with a link to activate your Plan.  You will probably receive it right away, but if you don't get it within a few hours, <a href=" . '"mailto:grinnellplans@gmail.com"' . ">Bug us</a>.";
	}

} else if ($_GET['token']) {
	$session = get_item($dbh, 'session', 'tentative_accounts', 'token' , $token);
	if (! $session) {
		echo 'That doesn\'t seem to be a valid or unexpired token, please try again or <a href="mailto:grinnellplans@gmail.com">Email</a> us.<br /> <hr />';
		show_form();
	} else {
		$data = unserialize($session);
		$username = $data['username'];
		$type = $data['type'];
		$year = $data['year'];
		$email = $username . '@grinnell.edu';
		if (get_item($dbh, 'username', 'accounts', 'username' , $username)) {
			echo 'A plan with the username ' . $username . ' already exists, meaning this token has been used.  If you are the owner of that email, your password was given to you when you first clicked the link.  If you\'ve lost the password, or for anything else, <a href="mailto:grinnellplans@gmail.com">Email</a> us.';
			show_form();
		} else {
			$results = insert_user($username, '', $year, $email, $type);
			$password = $results[0];
			echo "Your account has been created!  Your username is $username and your initial password is $password.  ";
			echo 'Go <a href="http://www.grinnellplans.com/">here</a> to test them out.';
			echo '<p>Lost? Confused? Just curious what the heck this Plans thing is?';
			echo 'You can read the [newbie] plan for a Plans crash course.';
			echo 'All you have to do is log in and type "newbie" (without the brackets) in the box in the upper-right-hand corner and click "Read".';
			echo '<p>An email with a copy of your password has also been emailed to you.';
			$message = "A new plan has been created with \nusername:  $username\nGrad Year: $year\n$username self-identifies as $type.";
			mail($admin_email, "Plan Created: $username", $message,
					"From:$admin_email\nReply-to:$admin_email");
			$message = "Your account has been created!  Your username is $username and your initial password is $password. Go to http://www.grinnellplans.com/ to get started.\n Don't forget that you can log in and read [newbie] or [help] any time you want a little guidance. You can also email us at grinnellplans@gmail.com.\n";
			mail("$email", "Plan Created", $message,
					"From:$admin_email\nReply-to:$admin_email");
		}
	}
} else {
	show_form();
}
?>


	<?php
if ($auth)
{
	mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl); //and send closing display data
}
else
{gdisp_end();}//if guest send guest closing display data

?>
</p>

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
</body>
</html>


<?php
function show_username_taken($username) {
	echo " Oh nO!, the username '$username' is already taken.  Please <a href=" . '"mailto:grinnellplans@gmail.com"' . ">Email</a> us and we'll make your account by hand.";
}
function show_form() {
	?>
		<p>
		If you have an @grinnell.edu email address for yourself or a student group, you may use this page to register a Plan for that username.<br />
		<b>If you are an alum</b>, please <a href="mailto:grinnellplans@gmail.com">Send us</a> your alumni.grinnell.edu email address and we will contact you through it with a username and password.  Please include your year of graduation, if any.<br />
		If you are somebody else, or have questions, <a href="mailto:grinnellplans@gmail.com">Ask us</a>, and we'll see what we can do.
		<br />
		<br />
		<br />
		</p>
		<p>  Enter your Grinnell username below (this is the part of your email address that comes before the '@', and click Register.  This will send you an email with a link that will complete your account creation.</p>
				<p>
				</p>
				<form name="signup" method="GET">
				<table>
				<tr><td>Grinnell email username:</td><td><input type="text" name="username"><br></td></tr>
				<tr><td>What is your relation to Grinnell? </td><td>
				<table>
				<tr><td>Student </td><td> <input type="radio" name="type" value="student" onClick ="toggle('year', 0);toggle('other', 4);">

				<span id="year"> Grad Year: <input type="text" name="gradyear"> </span>

				</td></tr>
				<tr><td>Staff  </td><td> <input type="radio" name="type" value="staff" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
				<tr><td>Group  </td><td> <input type="radio" name="type" value="group" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
				<tr><td>Faculty  </td><td> <input type="radio" name="type" value="faculty" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
				<!--
				<tr><td>Other  </td><td> <input type="radio" name="type" value="other" onClick ="toggle('year', 0);toggle('other', 4);"> <span id="other">
				Description: <input type="text" name="other" onkeyup="recount_chars()"> <i> max of 128 chars. So far you have typed <span id="char_count"></span></i>
				</span>
				-->


				</td></tr>
				</table>
				</td></tr>
				</table>
				<input type="submit" value="Register">
				<input type="hidden" value="1" name="submitted">
				</form>
				<?php
}
function make_token() {
	$length = 8;
	$token = '';
	for ($i = 0; $i < $length; $i++) {
		$next_int = rand(0,64);
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
		$token .= $next;
	}
	return $token;
}

?>
