<?php
require_once ('Plans.php');
if (isset($_GET['myprivl'])) {
    $level = (int)$_GET['myprivl'];
} else {
    $level = 1;
}
$_SESSION['glbs_lvl'] = $level;
if (isset($_GET["mark_as_read"]) && $_GET["mark_as_read"] == 1 && User::logged_in()) {
    mark_as_read($dbh, User::id(), $_SESSION['glbs_lvl']);
}
Redirect($_SERVER['HTTP_REFERER']);
?>
