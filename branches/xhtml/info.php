    Choed's suggestion:
    <?php
phpinfo();
echo (date("l F j, Y G:i T"));
echo "<br /><br /><br /><br /><br />";
?>
<?php
echo "\$TZ is $TZ";
echo "<br />";
echo "the date is ";
echo date('l dS \of F Y h:i:s A');
echo "<br />";
echo 'if I run "putenv ("TZ=$TZ");';
putenv("TZ=$TZ");
echo "<br />";
echo "then the date is ";
echo date('l dS \of F Y h:i:s A');
echo "<br />";
?>


