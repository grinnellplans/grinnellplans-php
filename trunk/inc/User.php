<?php
require_once ("Plans.php");
class User {
    public static function login($username, $password) {
        if (User::checkPassword($username, $password)) {
            $user = User::get($username);
            $user->login = mysql_timestamp();
            $user->save();
            $_SESSION['glbs_u'] = $user->username;
            $_SESSION['glbs_i'] = $user->userid;
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
        $newpass = crypt($password, $user->password);
        return ($newpass != '' && $newpass == $user->password);
    }
    /**
     * @return boolean true if password updated successfully
     */
    public static function changePassword($username, $newpassword, $oldpassword = null) {
        $user = User::get($username);
        if ($user->username != $username) return false;
        if (($oldpassword !== null) && ($user->password != crypt($oldpassword,$user->password)))
            return false;
	if (strlen($newpassword) < 4) return false;
        $user->password = User::hashPassword($newpassword);
        $user->save();
        return true;
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
                $user->email = $email;
                $user->save();
                return true;
        } else {
                return false;
        }
    }
    /**
     * @return string a one-way hash of the password, suitable for storage
     */
    public static function hashPassword($password) {
        return crypt($password);
    }

    public static function get($username = null) {
        if ($username !== null) {
                $user = Doctrine_Query::create()
                        ->from('Accounts a')
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
