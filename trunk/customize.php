<?php
	require_once("Plans.inc");

require_once("cookie_session.php");
require("functions-main.php");//load main functions
$dbh = db_connect();//connect to database

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if (!$auth) {
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell guest they can't edit
	gdisp_end();
} else {
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin valid user display

	//Give list of links of types of customization
	?>
	<center><h2>Preferences: </h2>
	<table>


	<tr><td><a
	href="autoread.php?myprivl=<?=$myprivl?>"
	class="main">change auto list</a></td></tr>


	<tr><td><a
	href="changepassword.php?myprivl=<?=$myprivl?>"
	class="main">change password</a></td></tr>

	<tr><td><a
	href="changename.php?myprivl=<?=$myprivl?>"
	class="main">change name</a></td></tr>

	<tr><td><a
	href="webview.php?myprivl=<?=$myprivl?>"
	class="main">guest readable</a></td></tr>


	<tr><td>&nbsp;</td></tr>

	<tr><td><p class="main">customize</p></td></tr>

	<tr><td><a 
	href="interfaces.php?myprivl=<?=$myprivl?>"
	class="lev2">interfaces</a></td></tr>

	<tr><td><a href="styles.php?myprivl=<?=$myprivl?>"
	class="lev2">styles</a></td></tr>

	<tr><td><a href="textbox.php?myprivl=<?=$myprivl?>" class="lev2">edit
	text box size</a></td></tr>


	<tr><td><a href="links.php?myprivl=<?=$myprivl?>" class="lev2">optional
	links</a></td></tr>

	</table>
	</center>
	<?


	mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//end display
}//if is a valid user
db_disconnect($dbh);
?>
