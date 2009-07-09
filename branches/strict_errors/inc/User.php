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
			$_SESSION['glbs_u'] = $user->username;
			$_SESSION['glbs_i'] = $user->userid;
			return $user;
		} else {
			return false;
		}
	}
	
	public static function get() {
		if (logged_in()) {
			return Doctrine::getTable('Accounts')->find($_SESSION['glbs_i']);			
		} else {
			throw new Exception('dunno');
		}
	}
	
	public static function logged_in() {
		return (isset($_SESSION['glbs_u']) && isset($_SESSION['glbs_i']) && ($_SESSION['glbs_i'] != 0));
	}
	
	public static function id() {
		if (isset($_SESSION['glbs_i'])) {
			return (int) $_SESSION['glbs_i'];
		} else {
			return false;
		}
	}
	
	public static function name() {
		if (isset($_SESSION['glbs_u'])) {
			return $_SESSION['glbs_u'];	
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
