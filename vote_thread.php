<?php
require_once("Plans.php");
require_once("Configuration.php");
require("functions-main.php");//load main functions
$dbh = db_connect();//establish the database handler

$idcookie = User::id(); 

if ( !User::logged_in()) 
{
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell person they can't log in
	gdisp_end();}//end guest display
else //elseallowed to edit
{
	if (!isset($_REQUEST['messageid'])) die("messageid must be specified"); 
	$messageid = (int)$_REQUEST['messageid'];
	$vote = isset($_REQUEST['vote'])?$_REQUEST['vote']:"";
        mysqli_query($dbh,"delete from boardvotes where userid = '$idcookie' and messageid = '$messageid'");
        if ($vote == "y") {
            mysqli_query($dbh,"insert into boardvotes (userid, threadid, messageid, vote) values ('$idcookie',(select threadid from subboard where messageid = '$messageid'), '$messageid', 1)");
        }
        if ($vote == "n") {
            mysqli_query($dbh,"insert into boardvotes (userid, threadid, messageid, vote) values ('$idcookie',(select threadid from subboard where messageid = '$messageid'), '$messageid', -1)");
        }
}
?>
Success
<?php
db_disconnect($dbh);
?>
