<?
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
        mysql_query("delete from boardvotes where userid = '$idcookie' and messageid = '$messageid'");
        if ($vote == "y") {
            mysql_query("insert into boardvotes (userid, threadid, messageid, vote) values ('$idcookie',(select threadid from subboard where messageid = '$messageid'), '$messageid', 1)");
        }
        if ($vote == "n") {
            mysql_query("insert into boardvotes (userid, threadid, messageid, vote) values ('$idcookie',(select threadid from subboard where messageid = '$messageid'), '$messageid', -1)");
        }
}
?>
Success
<?
db_disconnect($dbh);
?>
