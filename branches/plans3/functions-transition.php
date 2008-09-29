<?php
require_once('Plans.php');

function display_header() {
	$dbh = db_connect();
	if (User::logged_in()) {
		$idcookie = User::id();
		$myprivl = (isset($_GET['myprivl']) ? $_GET['myprivl'] : 1);
		mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
	} else {
		gdisp_begin($dbh);
	}
}

function display_footer() {
	$dbh = db_connect();
	if (User::logged_in()) {
		$idcookie = User::id();
		$myprivl = (isset($_GET['myprivl']) ? $_GET['myprivl'] : 1);
		mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $myprivl);
	} else {
		gdisp_end();
	}
}
?>