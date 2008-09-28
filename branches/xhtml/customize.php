<?

session_start();
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
	$preflist = cinterface_get_preferences_list();
	interface_disp_preferences($preflist);

	interface_disp_footer(false, who_just_updated());

	mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//end display
}//if is a valid user
db_disconnect($dbh);
?>
