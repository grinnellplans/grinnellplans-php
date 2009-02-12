<?php
require_once('Plans.php');

function display_header() {
	$dbh = db_connect();
	if (User::logged_in()) {
		$idcookie = User::id();
		mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
	} else {
		gdisp_begin($dbh);
	}
}

function display_footer() {
	$dbh = db_connect();
	if (User::logged_in()) {
		$idcookie = User::id();
		mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
	} else {
		gdisp_end();
	}
}

function get_myprivl() {
	if (isset($_SESSION['glbs_lvl'])) {
		return $_SESSION['glbs_lvl'];
	} else {
		return 1;
	}
}
?>
