<?php
/**
 * Redirects a user to a random plan.
 */
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');

if (User::logged_in()) {
    $ids_to_hide = Block::allUserIdsWithBlockingRelationships(User::id());
    array_push($ids_to_hide, User::id());
} else {
    $ids_to_hide = [];
}
$random_query = Doctrine_Query::create()
    ->select('a.username')
    ->from('Accounts a')
    ->leftJoin('a.Plan p')
    ->where('LENGTH(p.edit_text) != 0')
    ->andWhere('changed > DATE_SUB(NOW(), INTERVAL 1 YEAR)')
    ->andWhereNotIn('a.userid', $ids_to_hide);
$offset = rand(0,$random_query->count() - 1);
$random_user = $random_query->offset($offset)->limit(1)->fetchOne();
header("Location: read.php?searchname=" . $random_user->username);
?>
