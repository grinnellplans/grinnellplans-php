<?php
#echo "In auth.php <br />\n";
#print_r($_SESSION);


if ($_SESSION['kommand_auth']) {
#echo "kommand_auth is set";
	$gap = time() - $_SESSION['kommand_logged_in'];
#echo "gap is $gap<br />\n";
	if ($gap  > 1800 ) {
#echo "gap is too large<br />\n";
		redirect("Sorry, your session timed out." );
		exit;
	} else {
#echo "gap is not too large<br />\n";
	}
} else {
	redirect("Sorry, you don't seem to have a kommand session." );
#echo "kommand_auth is NOT set";
	exit;
}

function redirect($message) {
?>
<html>
<head>
<meta http-equiv="refresh" content="1;url=index.php">
</head>
<body>
<?=$message?>
</body>
</html>
<?php
}
?>
