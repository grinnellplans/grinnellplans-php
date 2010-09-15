<?php
require_once ('Plans.php');
new SessionBroker();
require ('functions-main.php');
require ("syntax-classes.php");
$dbh = db_connect(); //connect to the database
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'stylesheets', PLANSVNAME . ' - Stylesheets', 'style.php');
if (!User::logged_in()) {
    populate_guest_page($thispage);
    $denied = new AlertText('You are not allowed to see this as a guest.', 'Access Denied');
    $thispage->append($denied);
} //end guest display
else {
    $title = new HeadingText('Style Options:', 1);
    $thispage->append($title);
    $custom_style_form = '';
    if ($_POST['part']) //if they are submitting the form
    {
        if ($_POST['style'] == "custom") {
            if (!$_POST['urcss']) {
                $custom_style_form = new Form('customstyle', true);
                $custom_style_form->action = 'styles.php';
                $custom_style_form->method = 'POST';
                $thispage->append($custom_style_form);
                $item = new TextInput('urcss', get_item($dbh, "stylesheet", "stylesheet", "userid", $idcookie));
                $item->title = 'Custom Stylesheet URL:';
                $item->cols = 60;
                $custom_style_form->append($item);
                $item = new HiddenInput('style', 'custom');
                $custom_style_form->append($item);
                $item = new HiddenInput('part', '1');
                $custom_style_form->append($item);
                $item = new SubmitInput('Submit');
                $custom_style_form->append($item);
            } else {
                if (get_item($dbh, "stylesheet", "stylesheet", "userid", $idcookie)) {
                    set_item($dbh, "stylesheet", "stylesheet", $urcss, "userid", $idcookie);
                } else {
                    add_row($dbh, "stylesheet", array($idcookie, $urcss));
                }
                $message = new InfoText('Style Set', 'Success');
                $thispage->append($message);
            }
        } else {
            delete_item($dbh, "stylesheet", "userid", $idcookie);
            set_item($dbh, "display", "style", $style, "userid", $idcookie); //set the style that they selected
            $message = new InfoText('Style Set', 'Success');
            $thispage->append($message);
        } //if $style=custome, else
        
    }
    populate_page($thispage, $dbh, $idcookie);
    $my_result = mysql_query("Select style,descr From 
				  style"); //get currently available styles and their descriptions
    while ($new_row = mysql_fetch_row($my_result)) {
        $mystyles[] = $new_row;
    }
    $css = get_item($dbh, "stylesheet", "stylesheet", "userid", $idcookie);
    if ($css) {
        $customstyle = "checked";
    } else {
        $intcheck[get_item($dbh, "style", "display", "userid", $idcookie) ] = " checked"; //get the style that the user currently has selected, and set it to start out checked
        
    }
    $custom_style_form = new Form('styleform', true);
    $custom_style_form->action = 'styles.php';
    $custom_style_form->method = 'POST';
    $custom_style_form->title = 'Style Options';
    $thispage->append($custom_style_form);
    $item = new HiddenInput('part', '1');
    $custom_style_form->append($item);
    //begin the form
    $o = 0;
    while ($mystyles[$o][0]) {
        $item = new RadioInput('style', $mystyles[$o][0]);
        $item->checked = (strtolower($intcheck[$mystyles[$o][0]]) == ' checked');
        $name_and_desc = $mystyles[$o][1];
        $tmp_matches = array();
        preg_match('/<b>(.*)<\/b><br>(.*)/', $name_and_desc, $tmp_matches);
        $item->title = $tmp_matches[1];
        $item->description = $tmp_matches[2];
        $custom_style_form->append($item);
        $o++;
    }
    $item = new RadioInput('style', 'custom');
    $item->checked = ($customstyle == 'checked');
    $item->description = 'Custom Style Sheet';
    $custom_style_form->append($item);
    $item = new SubmitInput('Change');
    $custom_style_form->append($item);
} //if is a valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>
