<?php
	require_once("Plans.php");

if($_GET['jumbled'] == 'no') {
	setcookie('jumbled', 'no');
}

if($_GET['jumbled'] == 'yes') {
	setcookie('jumbled', 'yes');
}
?>
