<?php
require_once ("Plans.php");

$b = (int)$_GET['b'];
if (file_exists("buckets/$b.php")) {
	$_SESSION['b'] = (int)$_GET['b'];
} else {
	unset($_SESSION['b']);
}
if (isset($_SERVER['HTTP_REFERER'])) {
	Header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
	Header('Location: index.php');
}
?>
