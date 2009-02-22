<?php
require_once('Plans.php');

function get_myprivl() {
	if (isset($_SESSION['glbs_lvl'])) {
		return $_SESSION['glbs_lvl'];
	} else {
		return 1;
	}
}
?>
