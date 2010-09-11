<?php
require_once("Plans.php");

if(!USE_NATIVE_MAIL) {
	require_once("Mail.php");
}

class User {
	public static function login($username, $password) {
		if (User::checkPassword($username, $password)) {
			$user = Doctrine_Query::create()
							->from('Accounts a')
							->where('username = ?', $username)
							->fetchOne();
			$user->login = mysql_timestamp();
			$user->save();
			$_SESSION['glbs_u'] = $user->username;
			$_SESSION['glbs_i'] = $user->userid;
			return $user;
		} else {
			return false;
		}
	}

	/*
	 * Send a password reset email
	 * @param string $username the username supplied by the requestee
	 * @param string $email the email address supplied by the requestee
	 * @return boolean true if password reset sent successfully
	 */
	public static function resetPassword($username, $email) {
		$user = Doctrine_Query::create()
						->from('Accounts a')
						->where('username = ?', $username)
						->fetchOne();
		if ($user->email == $email) {
			for($i = 0; $i < 8; $i += 1) {
				$rnum = rand(0,62);
				if($rnum < 10) {
					$pass .= $rnum;
				} else if ($rnum < 36) {
					$pass .= chr($rnum + 55);
				} else {
					$pass .= chr($rnum + 61);
				}
			}
			$body = "Your new GrinnellPlans password is: " . $pass . "\n";
			$body .= "Please change your password to something you can remember.";
			if(USE_NATIVE_MAIL) {
				$subject = "Your new GrinnellPlans password!";
				$header = "From: " . MAILER_ADDRESS . "\n";
				if(mail($email, $subject, $body, $header)) {
					$user->password = User::hashPassword($mypassword);
					$user->save();
					return true;
				}
			} else {
				$header['From'] = MAILER_ADDRESS;
				$header['To'] = $email;
				$header['Subject'] = "Your new GrinnellPlans password!";
				$params['host'] = SMTP_SERVER_URI;
				$params['auth'] = SMTP_USE_AUTH;
				$params['username'] = SMTP_USERNAME;
				$params['password'] = SMTP_PASSWORD;
				$params['port'] = SMTP_SERVER_PORT;
				$mailer =& Mail::factory("smtp", $params);
				if($mailer->send($email, $header, $body)) {
					$user->password = crypt($pass);
					$user->save();
					return true;
				}
			}
		}	
		return false;
	}

	/**
	 * @return boolean true if the given password matched the stored password
	 */
	public static function checkPassword($username, $password) {
		$user = Doctrine_Query::create()
						->from('Accounts a')
						->where('username = ?', $username)
						->fetchOne();
		$newpass = crypt($password, $user->password);
		return ($newpass != '' && $newpass == $user->password);
	}

	/**
	 * @return string a one-way hash of the password, suitable for storage
	 */
	public static function hashPassword($password) {
		return crypt($password);
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
		$db = new Database();
		$privileges = $db->value_from_query("SELECT is_admin FROM accounts WHERE username = '" . User::name() . "'");
		if ($privileges) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function is_guest() {
		return !User::logged_in();
	}
	
	public static function logout() {
		session_destroy();
	}
}
?>
