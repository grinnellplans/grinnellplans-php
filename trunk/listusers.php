<?php
	require_once("Plans.php");

require_once("cookie_session.php");
require("functions-main.php");//load main functions
$dbh = db_connect();//connect to database

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];


if ($auth)
{
  mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin valid user display
}
else
{
  gdisp_begin($dbh);//begin guest user display
}

//if outside num range for letters, set to val for a
//also makes sure other things such as letters get
//wiped out
if (!(97 < $letternum) | !($letternum < 123))
{$letternum = 97;}
$letternum = round($letternum); // round in case decimal exists from user messing around

$i = 97; //set begin letter to a

while($i<123) //while before z
{
  if ($i==$letternum)//if we've hit the desire letter
    { echo "[" . chr($i) . "]"; //show that the letter is selected
    $current_letter = $i;
    }//if selected letter
  else //if not selected letter, make letter link to select that letter
    {echo " <a href= \"listusers.php?myprivl=" . $myprivl 
       . "&letternum=" . $i . "\">" . chr($i) . "</a> ";}
  $i++; //go on to next letter
}

echo "<br><hr>" . $showlist; //put letter and userlist together     

$arraylist = get_letters($dbh, chr($current_letter),
                         chr($current_letter + 1),$idcookie); //get usernames that start with that letter

//display those usernames
$j = 0;
while ($arraylist[$j][0])
{echo "<a href=\"read.php?myprivl=" . $myprivl . "&searchname=" .
   $arraylist[$j][1] . "\">" . $arraylist[$j][1] . "</a><br>";
 $j++;}



if ($auth)
{mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);}
else
{gdisp_end();}

db_disconnect($dbh);


?>
