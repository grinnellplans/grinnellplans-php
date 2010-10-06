<?php
require_once ("Plans.php");
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
