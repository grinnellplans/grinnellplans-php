<?php
require_once("Plans.php");
new SessionBroker();
	
class User {
	public static function logged_in() {
		return ($_SESSION['is_logged_in'] == 1);
	}
	
	public static function id() {
		return (int) $_SESSION['userid'];
	}
	
	public static function logout() {
		session_destroy();
	}
}
?>