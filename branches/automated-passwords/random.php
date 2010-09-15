<?php
/**
 * Redirects a user to a random plan.
 */
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');

$random_user = Doctrine_Query::create()->from('Accounts a')->leftJoin('a.Plan p')->where('LENGTH(p.edit_text) != 0')->orderby('RAND()')->limit(1)->fetchOne();
header("Location: read.php?searchname=" . $random_user->username);
?>
