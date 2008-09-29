<?php		 
require_once ("Plans.php");		 
new SessionBroker();		 
		 
require ("functions-main.php"); //load main functions		 
$dbh = db_connect(); //connect to the database		 
$idcookie = User::id();		 
if (!User::logged_in()) {		 
	gdisp_begin($dbh); //begin guest display		 
	echo ("You are not allowed to edit as a guest."); //tell user they can't edit as a guest		 
	gdisp_end();		 
} //end guest display		 
else		 
//allowed to edit		 
{		 
	if ($part) //if form has been submitted		 
	{		 
		set_item($dbh, "display", "interface", $interface, "userid", $idcookie); //set which interface they selected		 
		mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin valid user display		 
		//Let user know the interface has been set		 
		 
?>		 
   <center><h2>Interface Options:</h2>		 
      <table><tr><Td>Interface Set</td></tr></table></center><?php		 
	} else		 
	//if not submitted, give form		 
	{		 
		mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin valid user display		 
		$my_result = mysql_query("Select interface,descr From		 
interface"); //get the current interfaces and descriptions		 
		while ($new_row = mysql_fetch_row($my_result)) {		 
			$myinterfaces[] = $new_row;		 
		}		 
		$intcheck[get_item($dbh, "interface", "display", userid, $idcookie) ] = " checked"; //get user's current selection, and set it to the index of an array and put value to checked		 
		//begin the form		 
		 
?>		 
         <center><h2>Interface Options:</h2>		 
            <table><form action="interfaces.php" method="POST">		 
            <input type="hidden" name="part" value="1">		 
            <?php		 
		$o = 0;		 
		while ($myinterfaces[$o][0]) //loop through the options		 
		{		 
			echo "<tr><td><input type=\"radio\" name=\"interface\"		 
value=\"";		 
			echo $myinterfaces[$o][0] . "\"" . $intcheck[$myinterfaces[$o][0]] . "><td>" . $myinterfaces[$o][1] . "</td></tr>"; //show each option		 
			$o++;		 
		}		 
		//end form		 
		 
?>		 
          </table>		 
              <input type="submit" value="Change">		 
              </form>		 
              </center>		 
              <?php		 
	}		 
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //end valid user display		 
		 
} //if is a valid user		 
db_disconnect($dbh);		 
?>		 