<?php
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');
$dbh = db_connect(); //get the database connection
$idcookie = User::id();
if (User::logged_in()) {
    $theusername = get_item($dbh, "username", "accounts", "userid", $idcookie);
    header("Location: search.php?mysearch=" . $theusername . "&planlove=1"); //Send the user to that plan
    
} else {
    "You do not have a username to search for.";
}
db_disconnect($dbh);
?>


