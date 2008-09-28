<html>
<head>
</head>
<body>

<?php
log_tail();
function log_tail()
{
	$log_location = "chat/chat.talk";
	echo "<p>\n";
	$lines = `tail $log_location`;
	$lines = htmlentities($lines);
	$lines = preg_replace(array('/\n/'), array("<br \/>\n"), $lines);
	echo $lines;
	echo "</p>";
}
?>








</body>
