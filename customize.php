<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect(); //connect to database
$idcookie = User::id();
if (!User::logged_in()) {
	gdisp_begin($dbh); //begin guest display
	echo ("You are not allowed to edit as a guest."); //tell guest they can't edit
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin valid user display
	//Give list of links of types of customization
	
?>
	<center><h2>Preferences: </h2>
	<table>


	<tr><td><a
	href="autoread.php?myprivl=<?php
	echo $myprivl
?>"
	class="main">change auto list</a></td></tr>


	<tr><td><a
	href="changepassword.php?myprivl=<?php
	echo $myprivl
?>"
	class="main">change password</a></td></tr>

	<tr><td><a
	href="changename.php?myprivl=<?php
	echo $myprivl
?>"
	class="main">change name</a></td></tr>

	<tr><td><a
	href="webview.php?myprivl=<?php
	echo $myprivl
?>"
	class="main">guest readable</a></td></tr>


	<tr><td>&nbsp;</td></tr>

	<tr><td><p class="main">customize</p></td></tr>

	<tr><td><a href="styles.php?myprivl=<?php
	echo $myprivl
?>"
	class="lev2">styles</a></td></tr>

	<tr><td><a href="textbox.php?myprivl=<?php
	echo $myprivl
?>" class="lev2">edit
	text box size</a></td></tr>


	<tr><td><a href="links.php?myprivl=<?php
	echo $myprivl
?>" class="lev2">optional
	links</a></td></tr>

	</table>
	</center>
	<?php
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //end display
	
} //if is a valid user
db_disconnect($dbh);
?>
