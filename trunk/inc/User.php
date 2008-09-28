<?php
require_once ("Plans.php");

class User {
	public static function logged_in() {
		return ($_SESSION['is_logged_in'] == 1);
	}
	
	public static function logout() {
		session_destroy();
	}
}

?>