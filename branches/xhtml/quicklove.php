<?

session_start();
require("functions-main.php");//load main functions
$dbh = db_connect();//get the database connection

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ( $auth)
{

$theusername = get_item($dbh,"username","accounts","userid", $idcookie);

header("Location: search.php?mysearch=" . $theusername . "&planlove=1");//Send the user to that plan
}
else
{"You do not have a username to search for.";}

db_disconnect($dbh);


?>


