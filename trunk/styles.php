<?php
	require_once("Plans.inc");

require_once("cookie_session.php");
require("functions-main.php");//load main functions
$dbh = db_connect();//connect to the database


$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ( ! $auth) {
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell them they can't do anything here
	gdisp_end();}//end guest display
  else {

		$custom_style_form = '';
	  if ($part)//if they are submitting the form
	  { 

		  if ($style=="custom") {

			  if (!$urcss) {

				  $custom_style_form = 'Custom Stylesheet URL: <br />';
				  $custom_style_form .= '<form action="styles.php" method="post">';
				  $custom_style_form .= '<input type="text" size="60" name="urcss" value="' . get_item($dbh, "stylesheet","stylesheet","userid", $idcookie) . '">';
				  $custom_style_form .= '<input type="hidden" name="style" value="custom"><input type="hidden" name="part" value="1"><input type="submit" value="Submit"></form>';

			  } else {

				  if (get_item($dbh, "stylesheet","stylesheet","userid", $idcookie)) {
					  set_item($dbh, "stylesheet", "stylesheet", $urcss, "userid", $idcookie);
				  } else {
					  add_row($dbh, "stylesheet", array($idcookie, $urcss));
				  }

			  }

		  } else {

			  delete_item($dbh, "stylesheet", "userid", $idcookie);
			  set_item($dbh, "display", "style", $style,
					  "userid", $idcookie);//set the style that they selected

		$custom_style_form = ' <center><h2><i>Style Set</i></h2>  </center> ';

		  }//if $style=custome, else
	  }

		mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin valid user display
		echo $custom_style_form;
		  $my_result = mysql_query("Select style,descr From 
				  style");//get currently available styles and their descriptions

			  while($new_row = mysql_fetch_row($my_result)) {
				  $mystyles[] = $new_row;
			  }

		  $css = get_item($dbh, "stylesheet","stylesheet","userid", $idcookie);

		  if ($css) {$customstyle= "checked";}      
		  else {
			  $intcheck[get_item($dbh,"style","display","userid", $idcookie)] = " checked";//get the style that the user currently has selected, and set it to start out checked
		  }

		  ?>
			  <center><h2>Style Options:</h2>
			  <table><form action="styles.php" method="POST">
			  <input type="hidden" name="part" value="1">
			  <?//begin the form
			  $o=0;
		  while ($mystyles[$o][0]) {
			  echo "<tr><td><input type=\"radio\" name=\"style\" 
				  value=\"";
			  echo $mystyles[$o][0] . "\"" . $intcheck[$mystyles[$o][0]] . 
				  "></td><td>" . $mystyles[$o][1] . "</td></tr>";//give options 
			  $o++;
		  }
		  echo "<tr><td><input type=\"radio\" name=\"style\" 
			  value=\"custom\" $customstyle></td><td><b>Custom Style Sheet</b></td></tr>";

		  ?>
			  </table>
			  <input type="submit" value="Change">
			  </form>
			  </center>
			  <?



	  mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//end valid user display
  }//if is a valid user

db_disconnect($dbh);


?>
