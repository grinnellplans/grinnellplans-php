<?php
require_once('Plans.php');
new SessionBroker();
/**
 * @todo collapse autoread.php and this one
 */
require('functions-main.php');
$dbh = db_connect(); //connect to the database
$idcookie = User::id();

if (!User::logged_in()) {
	gdisp_begin($dbh); 
	echo ("You do not have an autoread list as a guest."); //tell guest they can't do anything on thsi page
	gdisp_end(); 
	
} else

{
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //begin valid user display
	//get old autofinger list
	$arlist = get_items($dbh, "interest,priority", "autofinger", "owner", $idcookie); //get the contents of a person's autofinger list
	$arraylist = get_letters($dbh, chr($letternum), chr($letternum + 1), $idcookie); //get usernames that start with that letter
	$o = 0;
	while ($arraylist[$o][0]) {
		$alist[$arraylist[$o][0]][0] = 1; //Get user id numbers for everyone whose name starts with the letter, and make the idnumbers the first index number of a new array, set the second index number to 0, and set the value to boolean 1
		$alist[$arraylist[$o][0]][1] = $arraylist[$o][1]; //if the second index number is 1, set the user's name as the value
		$o++;
	}
	//Loop through submitted data and create a new array with the index number being a plans userid number, and the value is the priority level
	while (list($key, $val) = each($HTTP_POST_VARS)) {
		if ($key > 0 and $key < 9999 and $val > 0) {
			$prosplist[$key] = $val;
		}
	}
	//checks to see what autoread stuff needs to be deleted
	$o = 0;
	while ($arlist[$o][0]) //loop through old autofinger list
	{
		if ($alist[$arlist[$o][0]][0] == 1) //if starts with right letter
		{
			if ($prosplist[$arlist[$o][0]]) {
				if ($arlist[$o][1] == $prosplist[$arlist[$o][0]]) //if priorities match
				{
					$remain[$arlist[$o][0]] = 1;
				} //set to neither delete, nor add
				else
				//if priorities don't match
				{
					$ridof[] = $arlist[$o][0];
				}
			} else
			//if not on prospective list
			{
				$ridof[] = $arlist[$o][0];
			}
		} //if starts with right letter
		$o++;
	} //while ($arlist[$o][0])
	//Set up an array of all of the autoread entrees that need to be added (not adding stuff that is already there)
	if (count($prosplist) > 0) {
		while (list($key, $val) = each($prosplist)) {
			if (!$remain[$key] == 1) {
				$addlist[] = $key;
			}
		}
	}
	//delete the autoread entrees that the person no longer wants or is changing the priority level for
	if ($ridof) {
		while (list($key, $val) = each($ridof)) {
			mysql_query("DELETE FROM autofinger WHERE
owner = '$idcookie' and interest = '$val'");
		}
	}
	//loop through and add the new autoread entrees
	if ($addlist) {
		while (list($key, $val) = each($addlist)) {
			$rew = array($idcookie, $val, $prosplist[$val], "", "", "");
			add_row($dbh, "autofinger", $rew);
		}
	}
	$i = 97; //set begin letter to a
	while ($i < 123) //while before z
	{
		if ($i == $letternum) //if we've hit the desire letter
		{
			echo "[" . chr($i) . "]"; //show that the letter is selected
			
		} else {
			echo " <a href= \"autoread.php?letternum=" . $i . "\">" . chr($i) . "</a> ";
		}
		$i++;
	}
	echo "<HR>" . "AutoRead List Changed.";
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
}
db_disconnect($dbh);
?>
