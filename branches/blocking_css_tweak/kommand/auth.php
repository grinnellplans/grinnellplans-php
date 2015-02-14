<?php
require_once ("../Plans.php");
if (User::is_admin()) {
} else {
    redirect_kommand("Sorry, you don't seem to have a kommand session.");
    exit;
}
function redirect_kommand($message) {
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
