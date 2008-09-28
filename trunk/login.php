<?php
require_once ("Plans.php");
new SessionBroker();

require ("functions-main.php");
$dbh = db_connect();

if (isset($_GET['logout'])) {
	User::logout();
	$msg = 'You have been successfully logged out.';
}

if (isset($_POST['username']) && isset($_POST['password'])) {
	if (!User::login($_POST['username'], $_POST['password'])) {
		$msg = "Invalid username or password.";
	}
}

Redirect('home.php');
?>