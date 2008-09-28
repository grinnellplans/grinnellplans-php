<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect();
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
if (!$auth) {
	gdisp_begin($dbh);
	echo ("You are not allowed to edit as a guest.");
	gdisp_end();
} else
//allowed to edit
{
	if ($part) {
		if ($webview != 1) {
			$webview = 0;
		}
		set_item($dbh, "accounts", "webview", $webview, "userid", $idcookie);
		mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
?>
		<center><h2>Guest Viewable:</h2>  
		<table><tr><Td>Preference set.</td></tr></table></center><?php
	} else {
		mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
		if (get_item($dbh, "webview", "accounts", "userid", $idcookie) == 1) {
			$viewable = " checked";
		} else {
			$unviewable = " checked";
		}
?>
		<center><h2>Webview:</h2>
		<table><form action="webview.php" method="POST">
		<input type="hidden" name="part" value="1">
		<?php
		echo "<tr><td><input type=\"radio\" name=\"webview\" 
		value=\"1\" " . $viewable . ">Make plan viewable to guests.</td></tr>";
		echo "<tr><td><input type=\"radio\" name=\"webview\" value=\"\" " . $unviewable . ">Make plan unviewable to guests.</td></tr>";
?>
		</table>
		<input type="submit" value="Change">
		</form>
		</center>
		<?php
	}
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl);
} //if is a valid user
db_disconnect($dbh);
?>

