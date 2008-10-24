<?php
require_once('Plans.php');

if (isset($_GET['myprivl'])) {
	$level = (int) $_GET['myprivl'];
} else {
	$level = 1;
}

$_SESSION['lvl'] = $level;

if (isset($_GET["mark_as_read"]) && $_GET["mark_as_read"] == 1 && $_SESSION['is_logged_in']==true) {
    mark_as_read($dbh, $idcookie, $_SESSION['lvl']);
}

Redirect($_SERVER['HTTP_REFERER']);
?>
