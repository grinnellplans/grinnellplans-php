<?php
require_once ('Plans.php');
function insert_user($added_name, $password, $gradyear, $email, $type, $status = '') {
    if (!$password) {
        srand(time());
        $password = rand(0, 999999);
    }
    if (!$email) {
        $email = $added_name . "@grinnell.edu";
    }
    $crpassword = User::hashPassword($password);
    $dbh = db_connect();
    $myrow = array("", $added_name, "", $crpassword, $email, "", "", "", "", "", "", $gradyear, "70", "14", "", "", $type, "", "", 0);
    add_row($dbh, "accounts", $myrow);
    mysqli_query($dbh,"UPDATE accounts SET created = NOW() WHERE
			username = '$added_name'");
    $added_id = get_item($dbh, "userid", "accounts", "username", $added_name);
    mysqli_query($dbh,"INSERT INTO plans (user_id) VALUES ($added_id)");
    add_row($dbh, "display", array($added_id, "6", "7"));
    foreach(array(2, 4, 6, 8, 14, 15, 16) as $opt_link) {
        $myrow = array($added_id, $opt_link);
        add_row($dbh, "opt_links", $myrow);
    }
    $myrow = array($added_id, $status);
    add_row($dbh, "perms", $myrow);
    return array($password, $email);
}
?>
