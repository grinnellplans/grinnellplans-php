<?php
require_once ('Plans.php');
function timestamp() {
    return date('YmdHis');
}
function mysql_timestamp() {
    return date('Y-m-d H:i:s');
}
?>