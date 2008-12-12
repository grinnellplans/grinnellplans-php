<?php
session_start();
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$dbh = db_connect();
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
// initialize page classes
$thispage = new PlansPage('Preferences', 'autoreadedit', PLANSVNAME . ' - Change Autoread', 'autoread.php');
if (!$auth) {
	get_guest_interface();
	populate_guest_page($thispage);
	$denied = new AlertText('You do not have an autoread list as a guest.', 'Access Denied');
	$thispage->append($denied);
} else
//allowed to edit
{
	get_interface($idcookie);
	populate_page($thispage, $dbh, $idcookie);
	$arlist = get_items($dbh, "interest,priority", "autofinger", "owner", $idcookie); //get their autoread info
	$o = 0;
	while ($arlist[$o][0]) {
		$autolist[$arlist[$o][0]][0] = 1; //set up an array with the first index value being the id number of the plan the person is interested in, the second index number being 0, and the actual value being the boolean true, 1
		$autolist[$arlist[$o][0]][1] = $arlist[$o][1]; //set it so that if the second index number is 1, then value contained will be the priority level
		$o++;
	}
	//////////////////////////////////////////////////////////////////
	$letternum = $_GET['letternum'];
	//check to make sure is a valid number
	if (!(97 < $letternum) |!($letternum < 123)) {
		$letternum = 97;
	} //if not, set to a
	$letternum = round($letternum); // round in case decimal exists from user messing around
	$i = 97; //set begin letter to a
	$alphabet = new WidgetGroup('autoread_alphabet', true);
	while ($i < 123) //do while before z
	{
		if ($i == $letternum) //if we've hit the desire letter
		{
			$letter = new RegularText("[" . chr($i) . "]", null);
			//echo "[" . chr($i) . "]"; //show that the letter is selected
			$current_letter = $i;
		} else
		//if not selected letter, make letter link to select that letter
		{
			$letter = null;
			$letter = new Hyperlink('letterlink_' . chr($i), true, "autoread.php?letternum=$i", chr($i));
		}
		$alphabet->append($letter);
		$i++; //go on to next letter
		
	}
	$thispage->append($alphabet);
	$arraylist = get_letters($dbh, chr($current_letter), chr($current_letter+1), $idcookie); //get usernames that start with that letter
	// Make our form
	$listform = new Form('autoreadlistform', true);
	$thispage->append($listform);
	$listform->action = 'proc_autoread.php';
	$listform->method = 'POST';
	$arlist = get_items($dbh, "interest,priority", "autofinger", "owner", $idcookie); //get their autoread info
	//display those usernames
	$j = 0;
	while ($arraylist[$j][0]) //do while there are names to display
	{
		$buttonlist = new WidgetGroup('autoreadbuttonlist', false);
		if ($arraylist[$j][0] != $idcookie) //don't display name if the name is the user's name
		{
			$mypriority[0] = "";
			$mypriority[1] = "";
			$mypriority[2] = "";
			$mypriority[3] = "";
			if ($autolist[$arraylist[$j][0]][0] == 1) {
				$mypriority[$autolist[$arraylist[$j][0]][1]] = " checked";
			} //if the current name is on the person's autoread list, set the array so that the value attributed with the index number of the priority level will be the string "checked" to show what is currently set.
			else {
				$mypriority[0] = " checked";
			}
			$buttons = new WidgetGroup('autoreadbuttons', false);
			for ($a = 0; $a < 4; $a++) {
				$item = new FormItem('radio', $arraylist[$j][0], $a);
				$item->checked = (" checked" == $mypriority[$a]);
				if ($a == 0) $item->description = "X";
				else $item->description = $a;
				//$listform->appendField($item);
				$buttons->append($item);
			}
			$letter = new RegularText($arraylist[$j][1], 'username');
			$buttons->append($letter);

			$buttonlist->append($buttons);
		}
		$listform->append($buttonlist);
		$j++;
	}
	//pass on other info
	$item = new FormItem('hidden', 'set_autoreadlist', $idcookie);
	$listform->append($item);
	$item = new FormItem('hidden', 'letternum', $letternum);
	$listform->append($item);
	$item = new FormItem('submit', NULL, 'Submit');
	$listform->append($item);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>




