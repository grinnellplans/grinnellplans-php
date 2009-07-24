<?php
require_once ("../Plans.php");
if ($_SESSION['kommand_auth']) {
	$gap = time() - $_SESSION['kommand_logged_in'];
	if ($gap > 1800) {
		redirect_kommand("Sorry, your session timed out.");
		exit;
	}
} else {
	redirect_kommand("Sorry, you don't seem to have a kommand session.");
	exit;
}
function redirect_kommand($message)
{
?>
<html>
<head>
<meta http-equiv="refresh" content="1;url=index.php">
</head>
<body>
<?php echo $message
?>
</body>
</html>
<?php
}
?>
