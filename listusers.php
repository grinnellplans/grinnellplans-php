<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect();
$idcookie = User::id();
$thispage = new PlansPage('Utilities', 'listusers', PLANSVNAME . ' - List All Plans', 'listusers.php');
if (User::logged_in()) {
	populate_page($thispage, $dbh, $idcookie);
	
} else {
	populate_guest_page($thispage);
	
}
//if outside num range for letters, set to val for a
//also makes sure other things such as letters get
//wiped out
if (!(97 < $letternum) | !($letternum < 123)) {
	$letternum = 97;
}
$letternum = round($letternum); // round in case decimal exists from user messing around
$i = 97; //set begin letter to a
$alphabet = new WidgetGroup('listusers_alphabet', true);
$thispage->append($alphabet);
while ($i < 123) //while before z
{
	if ($i == $letternum) //if we've hit the desire letter
	{
		$letter = new RegularText("[" . chr($i) . "]", null);
		$current_letter = $i;
	} //if selected letter
	else
	//if not selected letter, make letter link to select that letter
	{
		$letter = new Hyperlink('letterlink_' . chr($i), true, "listusers.php?letternum=$i", chr($i));
	}
	$alphabet->append($letter);
	$i++; //go on to next letter
	
}
$arraylist = get_letters($dbh, chr($current_letter), chr($current_letter + 1), $idcookie); //get usernames that start with that letter
//display those usernames
$j = 0;
$buttonlist = new WidgetList('listusers_buttonlist', true);
$thispage->append($buttonlist);
while ($arraylist[$j][0]) {
	$name = new PlanLink($arraylist[$j][1]);
	$buttonlist->append($name);
	$j++;
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
