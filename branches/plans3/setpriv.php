<?php
require_once('Plans.php');

if (isset($_GET['myprivl'])) {
	$level = (int) $_GET['myprivl'];
} else {
	$level = 1;
}

$_SESSION['lvl'] = $level;

Redirect($_SERVER['HTTP_REFERER']);
?>