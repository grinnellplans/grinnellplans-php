<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
$dbh = db_connect(); //get the database connection
$idcookie = User::id();

$q = Doctrine_Query::create()
    ->from('Accounts a')
    ->leftJoin('a.Plan p')
    ->where('LENGTH(p.edit_text) != 0')
    ->orderby('RAND()')
    ->limit(1);
$user = $q->fetchOne();
header("Location: read.php?searchname=" . $user->username); //Send the user to that plan
db_disconnect($dbh);
?>
