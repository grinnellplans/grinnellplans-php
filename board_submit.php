<?
	require_once("Plans.php");

require("functions-main.php");//load main functions
$dbh = db_connect();//establish the database handler

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ( ! $auth) 
{
	gdisp_begin ($dbh);		//begin guest display
	echo ("You are not allowed to edit as a guest.");	//tell person they can't log in
	gdisp_end ();
}				//end guest display
else //elseallowed to edit
{
	$showform=1;
	$error_message = '';
	if ($submit)
	{
		$showform=0;

		if ($newthread)
		{
			$threadtitle = cleanText($threadtitle);
			$threadtitle = preg_replace('/<[^>]*>/', '', $threadtitle);
			if (!$threadtitle || preg_match("/JLW/", $threadtitle))
			{
				$showform=1;
				$error_message =  "You are creating a new thread. Please enter a title for the thread.<br>";
			}//if no threadtitle
		}//if newthread
		else
		{
			if(!(get_item($dbh,"threadid","mainboard","threadid", $threadid)))
			{
				$showform=1;
				$error_message = "Invalid parent thread.<br>";
			}
		}//if not new thread

		$messagetitle=cleanText($messagetitle);

		$messagecontents=cleanText($messagecontents);
		if (!$messagecontents)
		{$showform=1;
		$error_message = "Please enter a message.<br>";
	}

	if (!$showform)
	{

		if ($newthread)
		{
			$my_result = mysql_query("Select threadid FROM mainboard WHERE lastupdated < DATE_SUB(NOW(), INTERVAL 7 DAY)");
			while($new_row = mysql_fetch_row($my_result)) {
				//delete_item($dbh, "subboard", "threadid", $new_row[0]);
			}

			//mysql_query("DELETE FROM mainboard WHERE lastupdated < DATE_SUB(NOW(), INTERVAL 7 DAY)");


			mysql_query("INSERT INTO mainboard VALUES(\"\",\"" . addslashes($threadtitle) . "\",NOW(),NOW(),\"" . $idcookie . "\")");
			$threadid=mysql_insert_id();

		}

		mysql_query("INSERT INTO subboard VALUES(\"\",\"" . $threadid . "\",NOW(),\"".$idcookie."\", \"".addslashes($messagetitle)."\", \"".addslashes($messagecontents)."\")");

		mysql_query("UPDATE mainboard SET lastupdated = NOW() WHERE threadid = \"" . $threadid . "\"");

		//			echo "Your message has been submitted.";

		//process message here
	}

}//if submit

if ($error_message || $showform) {
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin user display
	echo $error_message;
} else {
	header('Location: http://www.grinnellplans.com/board_messages.php?threadid=' . $threadid) ;
}


if ($showform)
{
	echo "<form action=\"board_submit.php\" method=\"POST\">";
	if ($newthread)
	{
		?>
		Thread Title:<br><input type="text" name="threadtitle" value="<?=$threadtitle?>"><br><br>
		<input type="hidden" name="newthread" value="1">
		<?
	}//if ($newthread)

	$boardsize = get_items($dbh,"edit_cols,edit_rows","accounts","userid",$idcookie);//get the users chosen box size 


	if ($boardsize[0][1]<1)
	{$boardsize[0][0]=70;}
	if ($boardsize[0][2]<1)
	{$boardsize[0][1]=14;}

	?>
	Message Title:<br><input type="text" name="messagetitle" value="<?=$messagetitle?>"><br><br>
	Message Contents:<br><textarea rows="<?=$boardsize[0][1]?>" cols="<?=$boardsize[0][0]?>" name="messagecontents" 
	wrap="virtual"><?=$messagecontents?></textarea><br>
	<input type="hidden" name="threadid" value="<?=$threadid?>">
	<input type="hidden" name="submit" value="1"><input type="submit" value="Submit Message"></form>
	<?
}//if showform

mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//gets user display
}//if valid user

db_disconnect($dbh);
?>
