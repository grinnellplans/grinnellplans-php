<?php
require_once('Plans.php');
require('functions-main.php');
$dbh = db_connect();
$idcookie = User::id();
if (!User::logged_in()) {
	gdisp_begin($dbh); 
	echo ("You are not allowed to edit as a guest."); 
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //begin logged in user display
	if ($changed == 1) //check to see if form has been submitted, process if so.
	{
		$user_name = htmlspecialchars($user_name); //strip out html chars
		set_item($dbh, "accounts", "pseudo", $user_name, "userid", $idcookie); //set pseudoname in database
		echo "Name changed to <b>" . stripslashes($user_name) . "</b>."; //tell user their name has been changed
		
	} //if changing name
	else { //if not changing name, give form
		$old_name = stripslashes(get_item($dbh, "pseudo", "accounts", "userid", $idcookie)); //get old name
		//display form
		
?>
	<center><h2>Change Name</h2>
	<form method="POST" action="changename.php">
	<input type="text" name="user_name" value="<?php
		echo $old_name; ?>">
			<input type="hidden" name="changed" value="1">
			<input type="submit" value="Change Name">
			</form>
			</center>
			<?php
	} //else, if not changing name, give form
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl()); //end logged in user display
	
} //if is a valid user
db_disconnect($dbh);
?>
