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
			$_SESSION['session_username'] = $user->username;
			$_SESSION['session_userid'] = $user->userid;
			return $user;
		} else {
			return false;
		}
	}
	
	public static function get() {
		if (logged_in()) {
			return Doctrine::getTable('Accounts')->find($_SESSION['session_userid']);			
		} else {
			throw new Exception('dunno');
		}
	}
	
	public static function logged_in() {
		return (isset($_SESSION['session_username']) && isset($_SESSION['session_userid']));
	}
	
	public static function id() {
		if (isset($_SESSION['session_userid'])) {
			return (int) $_SESSION['session_userid'];
		} else {
			return false;
		}
	}
	
	public static function name() {
		if (isset($_SESSION['session_username'])) {
			return $_SESSION['session_username'];	
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
