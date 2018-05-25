<?php
require_once ("Plans.php");
class User {
    public static function login($username, $password) {
        if (User::checkPassword($username, $password)) {
            $user = User::get($username);
            $user->login = new Doctrine_Expression('NOW()');
            $user->save();
            $_SESSION['glbs_u'] = $user->username;
            $_SESSION['glbs_i'] = $user->userid;
            session_regenerate_id(true);
            return $user;
        } else {
            return false;
        }
    }

    /**
     * @return boolean true if the given password matched the stored password
     */
    public static function checkPassword($username, $password) {
	$user = User::get($username);
        if ($user == false) return false;
        if (password_verify($password,$user->password)) {
            if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                $user->password = User::hashPassword($password);
                $user->save();
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return boolean true if password updated successfully
     */
    public static function changePassword($username, $newpassword, $oldpassword = null) {
        $user = User::get($username);
        if ($user->username != $username) return false;
        if (($oldpassword !== null) && (!password_verify($oldpassword,$user->password)))
            return false;
        if (strlen($newpassword) < 4) return false;
        $user->password = User::hashPassword($newpassword);
        $user->save();
        return true;
    }
    /**
     * @return string the user's new password.
     */
    public static function resetPassword($username, $expires, $hash, $password = null) {
        $user = User::get($username);
        $validhash = User::getPasswordResetHash($username,$expires,$user);
        if (!$validhash) return false;
        if ($expires < time()) return false;
        if ($hash != $validhash) return false;
        if ($user === false || $user->username != $username) return false;
        if ($password === null) {
                //If we don't get a password, generate an 8-character one for the user, using the Base64 character set (0-9A-Za-z+-.
                $password = base64_encode(pack("n*",mt_rand(0,65535),mt_rand(0,65535),mt_rand(0,65535)));
        }
        User::changePassword($user->username,$password);
        return $password;
    }
    /**
     * @return string the HMAC-SHA1 hash that's currently valid for the user, or false if the username doesn't exist.
     */
    public static function getPasswordResetHash($username, $expires, $user = null) {
        if ($user === null) $user = User::get($username);
        if ($user === false) return false;
        if (($user->Perms) && ($user->Perms->status == 'write-only')) return false;
        // Include current password in hash so link can only be used once
        $hashtext = "$user->username|$user->password|$expires";
        // Generate the hash, and base64-encode it to save space over hex-encoding
        return strtr(rtrim(base64_encode(hash_hmac('sha1',$hashtext,COOKIE_SIGNATURE_SALT,true)),'='),'+/','._');
    }
    /** 
     * @return string user's current email address 
     */
    public static function getEmail($username = null) {
        $user = User::get($username);
        if ($user === false) return false;
        return $user->email;
    }
    /**
     * @return boolean true if email updated successfully.
     */
    public static function setEmail($email, $username = null) {
        $user = User::get($username);
        if ($user === false) return false;
        if ($email != "") $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($email !== false) {
                $old = $user->email;
                $user->email = $email;
                $user->save();
                $sysadminemail = ADMIN_ADDRESS;
                $msg = <<<EOT
Hello [$user->username],

The email of record for your Plans account has been changed from "$old"
to "$email". It's important to have a correct email address, in case you
need to reset your password.

If $email isn't your email, email us at $sysadminemail pronto!

Your email is kept in the strictest confidence.

-The GrinnellPlans Administrators
EOT;
                //Only send mail to non-blank addresses; otherwise, send_mail fails
                if ($old !== "") send_mail($old, "Email address changed", $msg);
                if ($email !== "") send_mail($email, "Email address changed", $msg);
                return true;
        } else {
                return false;
        }
    }
    /**
     * @return string a one-way hash of the password, suitable for storage
     */
    public static function hashPassword($password) {
        return password_hash($password,PASSWORD_DEFAULT);
    }

    public static function get($username = null) {
        if ($username !== null) {
                $user = Doctrine_Query::create()
                        ->from('Accounts a')
                        ->leftJoin('a.Perms p')
                        ->where('username = ?', $username)
                        ->fetchOne();
                return $user;
        }
        else if (User::logged_in()) {
            return Doctrine_Query::create()->from('Accounts a')
                ->where('userid = ?', User::id())->fetchOne();            
        } else {
            return false;
        }
    }

    public static function logged_in() {
        return (isset($_SESSION['glbs_u']) && isset($_SESSION['glbs_i']) && ($_SESSION['glbs_i'] != 0));
    }

    public static function id() {
        if (isset($_SESSION['glbs_i'])) {
            return (int)$_SESSION['glbs_i'];
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
        $user = User::get();
        if (!$user) return false;
        else return $user->is_admin != 0;
    }

    public static function is_guest() {
        return !User::logged_in();
    }
    
    public static function logout() {
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(),"",time()-86400, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
		setcookie(session_name(),"",time()-86400, ini_get('session.cookie_path'), "", ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
	}
        session_destroy();
    }
}
?>
