<?php
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');
require ("syntax-classes.php");
$thispage = new PlansPage('Utilities', 'listusers', PLANSVNAME . ' - List All Plans', 'listusers.php');
if (User::logged_in()) {
    populate_page($thispage, $dbh, $idcookie);
} else {
    populate_guest_page($thispage);
}
$letter = isset($_GET['letter']) ? $_GET['letter'] : 'a';
$letter = substr($letter,0,1);
$letter = addcslashes($letter,'%_'); //no slipping wildcards into the LIKE clause
$alphabet = new WidgetGroup('listusers_alphabet', true);
$thispage->append($alphabet);
//Get list of all characters any plan name starts with
$letters = Doctrine_Query::create()->select('substr(a.username,1,1) as username')->from('Accounts a')->distinct()->execute();

foreach ($letters as $let) {
    $l = $let->username; //Hack hack hack
    if ($l == $letter) //if we've hit the desire letter
    {
        $label = new RegularText("[" . $l . "]", null);
    } //if selected letter
    else
    //if not selected letter, make letter link to select that letter
    {
        $label = new Hyperlink('letterlink_' . $l, true, "?letter=$l", $l);
    }
    $alphabet->append($label);
    
}
$users = Doctrine_Query::create()->select('username')->from('Accounts a')->where("username like concat(?,'%')",$letter);
if (!User::logged_in()) $users = $users->andWhere('webview = 1');
$users = $users->execute(); //get usernames that start with that letter
//display those usernames
$buttonlist = new WidgetList('listusers_buttonlist', true);
$thispage->append($buttonlist);
foreach ($users as $user) {
    $name = new PlanLink($user->username);
    $buttonlist->append($name);
}
interface_disp_page($thispage);
?>
