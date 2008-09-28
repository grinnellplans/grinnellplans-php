<?php
require_once ("../cookie_session.php");
$username = $_POST['username'];
require ("auth.php");
?>

<?php
$admin_email = "grinnellplans@gmail.com";
if ($whatoperation == "create") {
	$message = "An account for username " . $username . " with password " . $password . " has been set up for you.\n You can go to http://www.GrinnellPlans.com to log in.\nOnce you log in, you can change your password.";
	mail($email, "Your new plan.", $message, "From:$admin_email\nReply-to:$admin_email");
	echo "Message sent";
}
if ($whatoperation == "changepassword") {
	$message = "Password for plan account " . $username . " changed to " . $password;
	mail($email, "Plan password changed.", $message, "From:$admin_email\nReply-to:$admin_email");
	echo "Message sent";
}
?>
