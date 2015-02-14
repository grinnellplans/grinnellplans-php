<?php
require_once ('../Plans.php');
require_once ('auth.php');
require_once ('../functions-display.php');
$stylecount = array();
$ifacecount = array();
$combinecount = array();
$db = new Database();
$userid_res = $db->query("SELECT userid FROM accounts");
while ($userid_row = mysql_fetch_row($userid_res)) {
    $userid = $userid_row[0];
    $interfaceid = $db->value_from_query("SELECT interface.path FROM interface, display WHERE display.userid = " . $userid . " AND display.interface = interface.interface");
    $styleid = $db->value_from_query("SELECT style.path FROM style, display WHERE display.userid = " . $userid . " AND display.style=style.style");
    $style_custom = $db->get_item("stylesheet", "stylesheet", "userid", $userid);
    if ($style_custom) {
        $styleid = $style_custom;
    }
    $styleid = trim(strtolower($styleid));
    if (isset($stylecount[$styleid])) {
        $stylecount[$styleid]+= 1;
    } else {
        $stylecount[$styleid] = 1;
    }
    if (isset($ifacecount[$interfaceid])) {
        $ifacecount[$interfaceid]+= 1;
    } else {
        $ifacecount[$interfaceid] = 1;
    }
    $combine = "$interfaceid\t$styleid";
    if (isset($combinecount[$combine])) {
        $combinecount[$combine]+= 1;
    } else {
        $combinecount[$combine] = 1;
    }
}
function print_array($title, $array) {
    asort($array, SORT_NUMERIC);
    $array = array_reverse($array, TRUE);
    echo "<h1>$title</h1><table>";
    foreach($array as $k => $v) {
        echo "<tr><td>$k</td><td>$v</td></tr>";
    }
    echo "</table>";
}
print_array("Styles", $stylecount);
print_array("Interfaces", $ifacecount);
print_array("Interface/Style", $combinecount);
?>
</pre>
