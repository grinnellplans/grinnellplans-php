<?php
require_once('Plans.php');
require ("functions-main.php"); 
require ("syntax-classes.php");
$dbh = db_connect();
$idcookie = User::id();
// initialize page classes
$thispage = new PlansPage('Preferences', 'autoreadedit', PLANSVNAME . ' - Change Autoread', 'autoread.php');
if (!User::logged_in()) {
	populate_guest_page($thispage);
	$denied = new AlertText('You do not have an autoread list as a guest.', 'Access Denied');
	$thispage->append($denied);
} else
//allowed to edit
{
	populate_page($thispage, $dbh, $idcookie);
	$arlist = get_items($dbh, "interest,priority", "autofinger", "owner", $idcookie); //get their autoread info
	$o = 0;
	while ($arlist[$o][0]) {
		$autolist[$arlist[$o][0]][0] = 1;
		//set up an array with the first index value being the id number of the
		// plan the person is interested in, the second index number being 0, and
		// the actual value being the boolean true, 1
		$autolist[$arlist[$o][0]][1] = $arlist[$o][1];
		//set it so that if the second index number is 1, then value contained will be the priority level
		$o++;
	}
	//////////////////////////////////////////////////////////////////
	$letternum = $_GET['letternum'];
	//check to make sure is a valid number
	if (!(97 < $letternum) |!($letternum < 123)) {
		$letternum = 97;
	} //if not, set to a
	$letternum = round($letternum); // round in case decimal exists from user messing around
	$j = 97; //set begin letter to a
	$alphabet = new WidgetGroup('autoread_alphabet', true);
	while ($j < 123) //do while before z
	{
		if ($j == $letternum) //if we've hit the desire letter
		{
			$letter = new RegularText("[" . chr($j) . "]", null);
			//echo "[" . chr($j) . "]"; //show that the letter is selected
			$current_letter = $j;
		} else
		//if not selected letter, make letter link to select that letter
		{
			$letter = null;
			$letter = new Hyperlink('letterlink_' . chr($j), true, "autoread.php?letternum=$j", chr($j));
		}
		$alphabet->append($letter);
		$j++; //go on to next letter
		
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
	$buttonlist = new WidgetList('autoreadbuttonlist', false);
	while ($arraylist[$j][0]) //do while there are names to display
	{
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
			$buttons = new FormItemSet('autoreadbuttons', false);
			for ($a = 0; $a < 4; $a++) {
				$item = new RadioInput($arraylist[$j][0], $a);
				$item->checked = (" checked" == $mypriority[$a]);
				if ($a == 0) $item->description = "X";
				else $item->description = $a;
				//$listform->appendField($item);
				$buttons->append($item);
			}
			$buttons->title = $arraylist[$j][1];

			$buttonlist->append($buttons);
		}
		$j++;
	}
	$listform->append($buttonlist);
	//pass on other info
	$item = new HiddenInput('set_autoreadlist', $idcookie);
	$listform->append($item);
	$item = new HiddenInput('letternum', $letternum);
	$listform->append($item);
	$item = new SubmitInput('Submit');
	$listform->append($item);
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>




