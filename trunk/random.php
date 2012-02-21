<?php
/**
 * Redirects a user to a random plan.
 */
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');

$random_query = Doctrine_Query::create()->select('a.username')->from('Accounts a')->leftJoin('a.Plan p')->where('LENGTH(p.edit_text) != 0')->andWhere('changed > DATE_SUB(NOW(), INTERVAL 1 YEAR)');
$random_user = $random_query->offset(rand(0,$random_query->count()))->limit(1)->fetchOne();
header("Location: read.php?searchname=" . $random_user->username);
?>
