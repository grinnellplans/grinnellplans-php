<?php
require_once('Plans.php');
require('functions-main.php');
$dbh = db_connect();
$idcookie = User::id();
if (!User::logged_in()) {
	gdisp_begin($dbh); 
	echo ("You are not allowed to edit as a guest."); //tell guest they can't edit
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //begin valid user display
	//Give list of links of types of customization
	
?>
	<center><h2>Preferences: </h2>
	<table>


	<tr><td><a
	href="autoread.php"
	class="main">change auto list</a></td></tr>


	<tr><td><a
	href="changepassword.php"
	class="main">change password</a></td></tr>

	<tr><td><a
	href="changename.php"
	class="main">change name</a></td></tr>

	<tr><td><a
	href="webview.php"
	class="main">guest readable</a></td></tr>


	<tr><td>&nbsp;</td></tr>

	<tr><td><p class="main">customize</p></td></tr>

	<tr><td><a href="interfaces.php"class="lev2">interfaces</a></td></tr>

	<tr><td><a href="styles.php"
	class="lev2">styles</a></td></tr>

	<tr><td><a href="textbox.php" class="lev2">edit
	text box size</a></td></tr>


	<tr><td><a href="links.php" class="lev2">optional
	links</a></td></tr>

	</table>
	</center>
	<?php
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //end display
	
} //if is a valid user
db_disconnect($dbh);
?>
