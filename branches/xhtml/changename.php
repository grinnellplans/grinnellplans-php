<?
require("syntax-classes.php");

session_start();
require("functions-main.php");//load main functions
$dbh = db_connect();

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

// Create the new page
$page = new PlansPage('Preferences', 'planname', PLANSVNAME.' - Change Name', 'changename.php');

if ( ! $auth) 
{
	populate_guest_page($page);

	//tell them not able to use page
	$page->append(new AlertText("You are not allowed to edit this option as a guest.", false));

	/* display the page */
	get_guest_interface();
	interface_disp_page($page);
}
else //allowed to edit
{
	populate_page($page, $dbh, $idcookie);
	if ($changed==1)//check to see if form has been submitted, process if so.
	{
		$user_name = htmlspecialchars($user_name);//strip out html chars
		set_item($dbh, "accounts", "pseudo", $user_name,
		"userid", $idcookie);//set pseudoname in database

		$message = new InfoText("Name changed to <b>" . stripslashes($user_name) . "</b>.", NULL);//tell user their name has been changed
		$page->append($message);
	}//if changing name
	else { //if not changing name, give form
		$old_name = stripslashes(get_item($dbh,"pseudo","accounts","userid",
		$idcookie));//get old name

		/* create the page and form */
		$nameform = new Form('changename', 'Change Name');
		$page->append($nameform);
		$nameform->action = 'changename.php';
		$nameform->method = 'POST';

		/* add fields to the form */

		$item = new FormItem('text', 'user_name', $old_name);
		$item->datatype = Form::FIELD_TEXT;
		$nameform->appendField($item);

		$item = new FormItem('hidden', 'myprivl', $myprivl);
		$nameform->appendField($item);

		$item = new FormItem('hidden', 'changed', 1);
		$nameform->appendField($item);

		$item = new FormItem('submit', NULL, 'Change Name');
		$nameform->appendField($item);

	} //else, if not changing name, give form

	/* display the page */
	get_interface($idcookie);
	interface_disp_page($page);

	}//if is a valid user
db_disconnect($dbh);
?>
