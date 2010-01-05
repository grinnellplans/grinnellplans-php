<?php
require_once ("../Plans.php");
require ("auth.php");
?>

<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if ($_GET['submit']) {
    //print_r ($_GET);
    foreach(array_keys($_GET) as $get_key) {
        if (preg_match('/^(\w+)_id_(\d+)$/', $get_key, $matches)) {
            $secret_id = $matches[2];
            //      echo "Matched on id = $secret_id, got " . $_GET[$get_key] . ".<br />";
            if ($_GET[$get_key]) {
                $new_display = 'yes';
            } else {
                $new_display = 'no';
            }
            $query = "update secrets set display = '$new_display', date_approved = now() where secret_id = $secret_id";
            echo "<p>" . $query . "</p>";
            $results = mysql_query($query) or mysql_error();
            /*
            
            while($row = mysql_fetch_array($results)) {
            array_push($addresses, $row['address']);
            $total++;
            }
            */
        }
    }
}
$count = 60;
$offset = $_GET['offset'];
if ($offset < 0) {
    $offset = 0;
}
if (!($offset > 0)) {
    $offset = 0;
}
echo "<p><a href=\"secrets.php?offset=" . ($offset - 60) . "\">Next " . $count . "</a></p>";
echo "<p><a href=\"secrets.php?offset=" . ($offset + 60) . "\">Prev " . $count . "</a></p>";
$sql = "select * from secrets order by secret_id desc limit $offset, $count";
$secrets = mysql_query($sql);
if (!$secrets) {
    echo "Query Failed";
}
?>

<form method="GET" action="">
<input type="submit" name="submit" value="Submit">

<?php
while ($row = mysql_fetch_array($secrets)) {
    echo '<p class="sub">';
    $secret = $row['secret_text'];
    $date = $row['date'];
    $secret_id = $row['secret_id'];
    $display = $row['display'];
    echo "$secret_id <b>$date</b><br />\n";
    if ($display == 'yes') {
        //echo 'Display was yes.<br />';
        $yes_checked = ' checked="yes" ';
        $no_checked = ' ';
    } else {
        //echo 'Display was no.<br />';
        $yes_checked = ' ';
        $no_checked = ' checked="yes" ';
    }
    echo 'Show: <input type="radio" name="secrets_id_' . $secret_id . '" value="1"' . $yes_checked . '>';
    echo 'Hide: <input type="radio" name="secrets_id_' . $secret_id . '" value="0"' . $no_checked . '>';
    echo '<br />';
    echo "$secret\n";
}
echo '</p>';
?>


</form>
            <?php
db_disconnect($dbh);
?>
</body>
</html>
