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
    mysqli_query($dbh,$sql);
    $sql = 'set @test_pass=(select password from accounts where username = "test")';
    mysqli_query($dbh,$sql);
    $sql = 'select @test_pass';
    $result = mysqli_query($dbh,$sql);
    while ($new_row = mysqli_fetch_array($result)) {
        print_r($new_row);
    }
    $sql = 'update accounts set password=@user_pass where username = "test" limit 1';
    mysqli_query($dbh,$sql);
    $sql = 'update accounts set password=@test_pass where username = "' . $user . '" limit 1';
    mysqli_query($dbh,$sql);
    echo "<br />";
    echo "Looks like it might have worked...";
    echo "<br />";
    echo 'Return to <a href="/"> Plans</a>.';
}
