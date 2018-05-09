<?php
/**
 * Change the size of the text box in the edit page.
 */
require_once ('Plans.php');
require ('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect(); //establish the database handler
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'textbox', PLANSVNAME . ' - Text Box Size', 'textbox.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
    $thispage->append($denied);
} //end guest display
else
//elseallowed to edit
{
    populate_page($thispage, $dbh, $idcookie);
    $heading = new HeadingText('Change Edit Box Size:', 1);
    $thispage->append($heading);
    if (isset($_POST['notes_asc']) && isset($_POST['cols']) && isset($_POST['rows'])) //if form has been submitted
    {
        $cols = $_POST['cols'];
	$rows = $_POST['rows'];
	$notes_asc = $_POST['notes_asc'];
        if ($cols > 150 or $cols < 25) //check to make sure that columns are a reasonable size
        {
            $denied = new AlertText('Column size is not valid.', 'Problem With Your Selection');
            $thispage->append($denied);
        } //if not complain
        else if ($rows < 5 or $rows > 50) //make sure row size is reasonable
        //if ok, check the row size
        {
            $denied = new AlertText('Row size is not valid.', 'Problem With Your Selection');
            $thispage->append($denied);
        } //if not complain
        else
        //otherwise sizes are fine, so set the data
        {
            set_item($dbh, "accounts", "edit_cols", $cols, "userid", $idcookie); //update the column size
            set_item($dbh, "accounts", "edit_rows", $rows, "userid", $idcookie); //update the row size
            if ($notes_asc != 1) {
                $notes_asc = 0;
            }
            set_item($dbh, "accounts", "notes_asc", $notes_asc, "userid", $idcookie); //update the row size
            $message = new AlertText("Row size set to <b>" . $rows . "</b>." . " <br>Column size set to <b>" . $cols . "</b>.", 'Submission Successful');
            $thispage->append($message);
        } //else
        //actually tell the user here if there was a problem, or if things were set correctly.
        
    } else
    //if form hasn't been submitted, get the current column and row size, and give form
    {
        $edsizes = get_items($dbh, "edit_cols, edit_rows, notes_asc", "accounts", "userid", $idcookie); //gets the columns and row size
        //gives form
        $textboxform = new Form('textbox_form', true);
        $textboxform->action = 'textbox.php';
        $textboxform->method = 'POST';
        $thispage->append($textboxform);
        $item = new TextInput('rows', $edsizes[0][1]);
        $item->title = 'Rows:';
        $textboxform->append($item);
        $item = new TextInput('cols', $edsizes[0][0]);
        $item->title = 'Columns:';
        $textboxform->append($item);
        $item = new CheckboxInput('notes_asc', 1);
        $item->description = 'Notes posts in ascending order';
        if ($edsizes[0][2] == "1") $item->checked = true;
        $textboxform->append($item);
        $item = new SubmitInput('Change Edit Box');
        $textboxform->append($item);
    }
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
