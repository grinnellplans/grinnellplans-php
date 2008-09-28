<?
require_once("cookie_session.php");
require("functions-main.php");//load main functions
$dbh = db_connect(); ///connect to the database

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ( ! $auth) {
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell guest they can't edit
	gdisp_end();//end guest display
}
else //allowed to edit
{
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI, $myprivl, "edit.js");//begin valid user display

	if (!$part)//if nothing submitted yet
	{

		$myedit =
		get_items($dbh,"plan,edit_cols,edit_rows","accounts","userid",$idcookie);//get plan as well as what the size of the plan should be

		$plan = $myedit[0][0];//put the contents of the plan into the plan variable for easier use.


		//Make sure that the size of the edit plan is above 0
		if ($myedit[0][1]<1)
		{$myedit[0][1]=70;}
		if ($myedit[0][2]<1)
		{$myedit[0][2]=14;}


		//change <br>'s into \n's
		$plan = preg_replace("/<br>/", "", $plan); 
		$plan = preg_replace("/&amp;/s", "&", $plan);
		$plan = preg_replace("/amp;/s", "", $plan);
		$plan = preg_replace("/<hr><p.class=\"sub\">/","<hr>",$plan);


		//change plan links into regular form

		$plan =
		preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"planlove\"\>(.*?)\<\/a\>\]/s",
		"[\\1]",$plan);

		$plan =
		preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"boardlink\"\>(.*?)\<\/a\>\]/s",
		"[\\1]",$plan);



		//change plan links into regular form
		$plan =
		preg_replace("/\[\<a\shref=\"[^\"]*?\"\sclass=\"boardlink\"\>#(.*?)\<\/a\>\]/s",
		"[\\1]",$plan);

		//strip out CSS info from regular links on plans
		$plan =
		preg_replace("/\<a\shref=\"([^\"]*?)\"\sclass=\"onplan\"\>(.*?)\<\/a\>/s",
		"[\\1\|\\2]",$plan);

		//strip slashes that were added to plan to make sure the database didn't have any troubles with it
		$plan = stripslashes($plan);


		//give the form:
		?>
		<form action="edit.php" method="post" id="editform" name="editform">
		<textarea rows="<?=$myedit[0][2]?>" cols="<?=$myedit[0][1]?>" 
		name="plan" wrap="virtual" onkeyup="javascript:countlen();"><?
		echo $plan . "</textarea><input type=\"hidden\" name=\"part\" value=\"1\">";
		echo "<input type=\"hidden\" name=\"myprivl\" value=\"" . $myprivl . "\"><br>
		<img src=\"left.gif\" width=\"2\" height=\"16\"><img id=\"filled\" src=\"filled.gif\" width=\"0\" height=\"16\"><img id=\"unfilled\" src=\"unfilled.gif\" width=\"100\" height=\"16\"><img src=\"right.gif\" width=\"2\" height=\"16\"> <input type=\"text\" name=\"perc\" value=\"0%\" size=\"4\" style=\"border: 0px\" readonly>
		&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"Change Plan\"></form>";
		mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);

	}//if (!$part)
	else //if form info submitted, process
	{
		$diff_data =
		get_items($dbh,"edit_text","accounts","userid",$idcookie);//get plan as well as what the size of the plan should be

		$old_plan = $diff_data[0][0];//put the contents of the plan into the plan variable for easier use.
		$diff = xdiff_string_diff($old_plan, $plan);
		$diff = $diff;
		mysql_query("insert into diffs(userid, text, date) values($idcookie, \"$diff\", now())");
		//add_row($dbh, 'diffs', array($idcookie, $diff, 'now()'));
		set_item($dbh, "accounts", "edit_text", $plan, "userid", $idcookie);
		$plan=cleanText($plan);    


		set_item($dbh, "accounts", "plan", $plan, "userid", $idcookie);//set plan in database
		setUpdatedTime($idcookie);//set the time which keeps track of when the plan was last updated
		set_item($dbh, "autofinger", "updated", 1, "interest", $idcookie);//make the plan show up as updated on other people's autoread list.
		$plan = stripslashes($plan);//strip the slashes for display
		echo "<center><h2>Plan Changed To: </h2></center><p class=\"sub\">";
		echo $plan;//display new plan for user
		echo "</p>";


		mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//end valid user display

	}//if (!$part) else

}//allow to edit if user

db_disconnect($dbh);
?>

