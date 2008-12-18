<?php
require_once("Plans.php");

class User {
	public static function login($username, $password) {
		$user = Doctrine_Query::create()
						->from('Accounts a')
						->where('username = ? and password =?', array($username, crypt($password, 'ab')))
						->fetchOne();
		if ($user) {
			$user->login = timestamp();
			$user->save();
			$_SESSION['u'] = $user->username;
			$_SESSION['i'] = $user->userid;
			return $user;
		} else {
			return false;
		}
	}
	
	public static function get() {
		if (logged_in()) {
			return Doctrine::getTable('Accounts')->find($_SESSION['i']);			
		} else {
			throw new Exception('dunno');
		}
	}
	
	public static function logged_in() {
		return (isset($_SESSION['u']) && isset($_SESSION['i']));
	}
	
	public static function id() {
		if (isset($_SESSION['i'])) {
			return (int) $_SESSION['i'];
		} else {
			return false;
		}
	}
	
	public static function name() {
		if (isset($_SESSION['u'])) {
			return $_SESSION['u'];	
		} else {
			return USER_GUEST_NAME;
		}
	}
	
	public static function is_admin() {
		return true;
	}
	
	public static function is_guest() {
		return !User::logged_in();
	}
	
	public static function logout() {
		session_destroy();
	}
}
?>