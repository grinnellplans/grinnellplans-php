<?php
require_once ("../Plans.php");
require ("auth.php");
require ("../functions-kommand.php");
$username = $_POST['username'];
?>

<?php
$user = $_GET['user'];
if (!$user) {
?>
 <form action="" method="GET">
 <p >
Username to switch with test: 
<br />
<input type = "text" name="user">
</p>
</form>
<?php
} else {
    require ("dbfunctions.php");
    $dbh = db_connect();
    $sql = 'set @user_pass=(select password from accounts where username = "' . $user . '")';
    mysql_query($sql);
    $sql = 'set @test_pass=(select password from accounts where username = "test")';
    mysql_query($sql);
    $sql = 'select @test_pass';
    $result = mysql_query($sql);
    while ($new_row = mysql_fetch_array($result)) {
        print_r($new_row);
    }
    $sql = 'update accounts set password=@user_pass where username = "test" limit 1';
    mysql_query($sql);
    $sql = 'update accounts set password=@test_pass where username = "' . $user . '" limit 1';
    mysql_query($sql);
    echo "<br />";
    echo "Looks like it might have worked...";
    echo "<br />";
    echo 'Return to <a href="/"> Plans</a>.';
}
