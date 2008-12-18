<?php
require_once('Plans.php');
require ("functions-main.php"); 

$dbh = db_connect();
$idcookie = User::id();
if (!User::logged_in()) {
	gdisp_begin($dbh);
	echo ("You do not have an autoread list as a guest.");
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
?>
<form method="post" action="proc_autoread.php">
<?php
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
	//check to make sure is a valid number
	if (!(97 < $letternum) | !($letternum < 123)) {
		$letternum = 97;
	} //if not, set to a
	$letternum = round($letternum); // round in case decimal exists from user messing around
	$i = 97; //set begin letter to a
	while ($i < 123) //do while before z
	{
		if ($i == $letternum) //if we've hit the desire letter
		{
			echo "[" . chr($i) . "]"; //show that the letter is selected
			$current_letter = $i;
		} else
		//if not selected letter, make letter link to select that letter
		{
			echo " <a href= \"autoread.php?&letternum=" . $i . "\">" . chr($i) . "</a> ";
		}
		$i++; //go on to next letter
		
	}
	echo "<HR><BR>";
	$arraylist = get_letters($dbh, chr($current_letter), chr($current_letter + 1), $idcookie); //get usernames that start with that letter
	//display those usernames
	$j = 0;
	while ($arraylist[$j][0]) //do while there are names to display
	{
		if ($arraylist[$j][0] == $idcookie) //don't display name if the name is the user's name
		{
			echo "";
		} else
		//if name isn't the user's name, continue loop to display form
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
			echo " <input type=\"radio\" name=\"" . $arraylist[$j][0] . "\" value=\"0\"" . $mypriority[0] . ">X";
			echo " <input type=\"radio\" name=\"" . $arraylist[$j][0] . "\" value=\"1\"" . $mypriority[1] . ">1";
			echo " <input type=\"radio\" name=\"" . $arraylist[$j][0] . "\" value=\"2\"" . $mypriority[2] . ">2";
			echo " <input type=\"radio\" name=\"" . $arraylist[$j][0] . "\" value=\"3\"" . $mypriority[3] . ">3";
			echo "  " . $arraylist[$j][1] . "<BR>\n";
		}
		$j++;
	}
	//pass on other info
	echo "<input type=\"hidden\" name=\"set_autoreadlist\" value=\"" . $idcookie . "\">";
	echo "<input type=\"hidden\" name=\"letternum\" value=\"" . $letternum . "\">";
	echo "<input type=\"submit\" value=\"Submit\"></form>";
	/////endform here
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //end display
	
}
db_disconnect($dbh);
?>