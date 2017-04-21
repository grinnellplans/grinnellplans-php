<?php
require_once ('Plans.php');
function get_myprivl() {
    if (isset($_SESSION['glbs_lvl'])) {
        return $_SESSION['glbs_lvl'];
    } else {
        return 1;
    }
}
//////////
/*
setpriv - This function sets priviledge level
*/
function setpriv($myprivl, $cookpriv) {
    $_SESSION['glbs_lvl'] = $myprivl;
}
//////////
/*
*Sets a plan that's on a person's autoread list as read
*/
function update_read($dbh, $owner, $updated) {
    mysqli_query($dbh,"UPDATE autofinger SET updated = '0', readtime = NOW() WHERE owner = '$owner' and interest = '$updated'");
}
function mark_as_read($dbh, $owner, $myprivl) {
    $query = "UPDATE autofinger set updated = 0 where owner ='$owner' and priority = '$myprivl'";
    mysqli_query($dbh,$query);
}

function add_param($url, $name, $value) {
    if (strstr($url, $name)) {
        return preg_replace("/$name=[^&]*/", $name . '=' . $value, $url);
    } else {
        if (strstr($url,"?")) {
            return $url . '&amp;' . $name . '=' . $value;
        } else {
            return $url . '?' . $name . '=' . $value;
        }
    }
}
function remove_param($url, $name) {
    $url = preg_replace("/$name=[^&]*/", '', $url);
    $url = preg_replace("@&$@", '', $url);
    return $url;
}
?>
