<?php
require_once ("../Plans.php");
require_once ("../functions-email.php");
$username = $_POST['username'];
require ("auth.php");
?>

<?php
$admin_email = MAILER_ADDRESS;
if (!isset($_POST['whatoperation'])) die("Error: No operation specified.");
$whatoperation = $_POST['whatoperation'];
$email = $_POST['email'];
$password = $_POST['password'];

if ($whatoperation == "create") {
    $message = "An account for username " . $username . " with password " . $password . " has been set up for you.\n You can go to http://www.GrinnellPlans.com to log in.\nOnce you log in, you can change your password.";
    send_mail($email, "Your new plan.", $message, $admin_email, $admin_email)?echo "Message sent":echo "Message send failed";
}
if ($whatoperation == "changepassword") {
    $message = "Password for plan account " . $username . " changed to " . $password;
    send_mail($email, "Plan password changed.", $message, $admin_email, $admin_email);
    echo "Message sent";
}
?>
