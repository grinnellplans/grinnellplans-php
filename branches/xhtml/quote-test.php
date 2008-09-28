<?php
$var['b'] = "333";
$var['a'] = "'333";
print_r($var);
$s = serialize($var);
echo "<br />";
echo "<br />";
echo "$s <br />";
echo "<br />";
$t = unserialize($s);
print_r($t);
echo "<br />";
echo "<br />";
?>
