<?php
require_once ('../Plans.php');
require_once('auth.php');
require_once('../functions-email.php');
?>
<html>
<body>
<form method="POST" action="changeemail.php">
<?php
if (isset($_REQUEST['username']) && isset($_POST['email'])) {
$oldemail = User::get($_REQUEST['username'])->email;
if (User::setEmail($_POST['email'],$_REQUEST['username'])) {
$audit = User::get()->username." changed ".$_REQUEST['username']."'s email address.\n";
$audit.= "Former email address: ".$oldemail." \n";
$audit.= "New email address: ".$_REQUEST['email']." \n";
send_mail(ADMIN_ADDRESS,"Plans audit event: email for ".$_REQUEST['username']." changed", $audit);
echo 'Email address updated.';
} else {
echo 'Error updating email address. <a href="changeemail.php?username="'.htmlentities($_REQUEST['username']).'">Try again?</a>';
}//if update succeeded
} elseif (isset($_REQUEST['username']) && (($user = User::get($_REQUEST['username'])) !== false)) { ?>
Username : <?php echo $user->username; ?>
<input type="hidden" name="username" value="<?php echo htmlspecialchars($user->username); ?>" /><br />
Email Address : <input type="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" /> <br />
<input type="submit" value="Change Email" />
<?php } else { /*!isset(username) */ 
if (isset($_REQUEST['username'])) echo 'Invalid or nonexistent username.<br />';
?>
Username : <input type="text" name="username" value="<?php if (isset($_REQUEST['username'])) echo htmlspecialchars($_REQUEST['username']); ?>" /> 
<input type="submit" value="Look Up" /></form>
<?php } /* !isset(username) */ ?>
</form>
<br />Usage of this tool will send audit emails to other administrators, and will email the user's old and new addresses.
<br /><a href="index.php">Return to Kommand</a>
</body>
</html>
