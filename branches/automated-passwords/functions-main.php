<?php
require_once('Plans.php');
if (isset($_GET['jumbled'])) {
	if ($_GET['jumbled'] == 'no') {
		setcookie('jumbled', 'no');
	}
	if ($_GET['jumbled'] == 'yes') {
		setcookie('jumbled', 'yes');
	}
} else {
}

require_once ("Configuration.php");
require_once ("legal.php");
require_once ("dbfunctions.php");
require_once ("functions-autofinger.php");
require_once ("functions-display.php");
require_once ("functions-edit.php");
require_once ("functions-core.php");
require_once ("functions-email.php");
require ("core-perms.php");
?>
