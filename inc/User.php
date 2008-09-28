<?php
require_once("Plans.php");
new SessionBroker();

class User {
	public static function login($username, $password) {
		require ("functions-main.php");
		$db = new Database();
		$dbh = db_connect();
		if ($username) {
			if (get_items($dbh, "username", "accounts", "username", $username)) {
				$orig_pass = $password;
				$password = crypt($password, "ab");
				$read_pass = get_item($dbh, "password", "accounts", "username", $username);
				if ($password == $read_pass) {
					$idcookie = $db->get_item('accounts', 'userid', 'username', $username);
					mysql_query("UPDATE accounts SET login = NOW() WHERE userid = $idcookie");
					$_SESSION['is_logged_in'] = 1;
					$_SESSION['username'] = $username;
					$_SESSION['userid'] = $idcookie;
					$sql = "insert into js_status set userid = " . addslashes($idcookie) . ", status = '" . addslashes($_POST['js_test_value']) . "'";
					mysql_query($sql);
				} else {
					return false;
				}
			} else {
				return false;
			}
			if (!$_SESSION['is_logged_in']) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	public static function logged_in() {
		return (isset($_SESSION['is_logged_in']) && ($_SESSION['is_logged_in'] == 1));
	}
	
	public static function id() {
		return (int) $_SESSION['userid'];
	}
	
	public static function name() {
		return $_SESSION['username'];
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