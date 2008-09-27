<?

session_start();
require("functions-main.php");//load main functions
$dbh = db_connect();//establish the database handler

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

$threadsperpage =threadsperpage();

if ( ! $auth)
{
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell person they can't log in
	gdisp_end();}//end guest display
else //elseallowed to edit
{
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin user display

	echo "<center><a href=\"board_submit.php?newthread=1\" class=\"lev2\">New Thread</a>";
	$my_result = mysql_query("Select COUNT(*) From mainboard");
	$totalthreads = mysql_fetch_row($my_result);


	if (!($pagenumber>0))
	{$pagenumber=0;}

	if ($pagenumber > floor($totalthreads[0]/$threadsperpage))
	{$pagenumber=floor($totalthreads[0]/$threadsperpage);}


	if ($pagenumber>0)
	{
		echo "<a href=\"board_show.php?pagenumber=0\">&lt;&lt;</a> ";
	}
	else
	{echo "&lt;&lt; ";}


	if ($pagenumber>=2)
	{$tempnum=$pagenumber-2;
	echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";}
	else
	{echo "_ ";}


	if ($pagenumber>=1)
	{$tempnum=$pagenumber-1;
	echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";}
	else
	{echo "_ ";}

	echo "[" . $pagenumber . "] ";

	if ($totalthreads[0]>($pagenumber+1)*$threadsperpage)
	{$tempnum=$pagenumber+1;
	echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";}
	else
	{echo "_ ";}

	if ($totalthreads[0]>($pagenumber+2)*$threadsperpage)
	{$tempnum=$pagenumber+2;
	echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";}
	else
	{echo "_ ";}

	if (floor($totalthreads[0]/$threadsperpage)>$pagenumber)
	{echo "<a href=\"board_show.php?pagenumber=" . floor($totalthreads[0]/$threadsperpage) . 
	"\">&gt;&gt;</a></center>";}
	else
	{echo "&gt;&gt;</center>";}





	$rowoffset=$threadsperpage*$pagenumber;


	echo "<table class=\"boardshow\"><tr class=\"boardrow1\"><td><center><b>Title</b></center></td><td><center><b>Newest 
	Message</b></center></td><td><center><b># of Messages</b></center></td></tr>";

	$my_result = mysql_query("Select mainboard.threadid, mainboard.title, DATE_FORMAT(mainboard.lastupdated, '%a %M %D, 
	%l:%i %p'), COUNT(*) From 
	mainboard,subboard where subboard.threadid=mainboard.threadid and mainboard.threadid not in (645) GROUP BY threadid ORDER BY lastupdated DESC LIMIT " . 
	$rowoffset . "," . 
	$threadsperpage);

	$colorswitch=0;
	while($new_row = mysql_fetch_row($my_result)) {
		if ($colorswitch==0)
		{echo "<tr class=\"noteslight\">";
		$colorswitch=1;}
		else 
		{
			echo "<tr class=\"notesdark\">";
			$colorswitch=0;
		}

		echo "<td><a href=\"board_messages.php?threadid=" . $new_row[0] . "\">" .stripslashes($new_row[1])."</a></td><td>" . 
		$new_row[2] . "</td><td><center>" . $new_row[3] . "</center></td></tr>";
	}

	echo "</table>";




	mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//gets user display
}

db_disconnect($dbh);
?>
