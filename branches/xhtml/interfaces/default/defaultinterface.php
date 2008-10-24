<?php
/**
 * "Modern" Interface
 * The default Plans interface. Table-icious. Ick.
 */
/*
* Interface files must include the public function
*   interface_disp_page(PlansPage $page)
* which must handle a PlansPage object and everything it contains, as
* specified in syntax-classes.php.
*
*/
//DEPRECATED
include (realpath(dirname(__FILE__) . '/../common.php'));
//DEPRECATED
function interface_disp_begin($foo1, $foo2, $foo3, $foo4, $foo5, $foo6, $foo7) 
{
}
//DEPRECATED
function interface_disp_end($foo1) 
{
	echo '';
}
function interface_disp_page(PlansPage $page) 
{
?>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOARCHIVE">
<title><?php
	echo $page->title ?></title>  
<link rel=stylesheet
href="<?php
	echo $page->stylesheet ?>">

<?php
	disp_local_jsfiles($page);
?>
</head>
<body>

<table width="100%" cellspacing="0" cellpadding="0"
class="main">
<?php
	//print the mainpanel
	if ($page->mainpanel) disp_mainpanel($page);
?>
<td valign="top">

<br />

<table>

<tr><td>

<?php
	if ($page->group == 'Preferences' && $page->identifier != 'autoreadedit') {
		echo '<center>';
		$print_center = true;
	}
	// display the widgets in the contents of the page
	array_walk($page->contents, 'disp_widget');

	if ($print_center) {
		echo '</center>';
	}
	echo "</td></tr></table></td></tr></table>";
	if ($page->footer) disp_footer($page->footer);
	echo "\n\t</body>\n\n\n\n</html>";
} //function interface_disp_page
/*
* disp_local_jsfiles
*
* Examines the page identifier and prints includes for any javascript we
* traditionally use on the page.
*/
function disp_local_jsfiles($page) 
{
	$jsfile_arr = array();
	// Populate the array with any files we need
	switch ($page->identifier) {
		case 'edit':
			$jsfile_arr[] = 'edit.js';
			break;
	}
	// Now print them to the page
	foreach($jsfile_arr as $jsfile) {
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"$jsfile\"></script>";
	}
}
//TODO
function disp_widget($value, $key) 
{
	switch (get_class($value)) {
		case 'Form':
			print('<table><form method="' . $value->method . '" action="' . $value->action . '">');
			// treat certain forms differently
			if ($value->identifier == 'autoreadlistform') {
				foreach($value->contents as $item) {
					$special = ($item->type == 'hidden' || $item->type == 'submit');
					disp_widget($item, null);
					if (!$special) print('<BR>');
				}
				break;
			}

			foreach($value->contents as $item) {
				$special = ($item->type == 'hidden' || $item->type == 'submit');
				if (!$special) print("\n\t<tr><td>");
				disp_widget($item, null);
				if (!$special) print("</td></tr>");
			}
			print("\n</form></table>");
			print ($str);
			break;

		case 'FormItem':
			print($value->toHTML() . ' ');
			break;

		case 'Hyperlink':
			print (strtolower($value->toHTML()) . "\n");
			break;

		case 'InfoText':
			print ("\t<span class=\"info\">\n");
			if ($value->title && $value->title != '') print ("\t<span class=\"infotitle\">" . $value->title . "</span>\n");
			print ("\t" . $value->toHTML() . "\n");
			print ("\t</span>\n");
			break;

		case 'RegularText':
			print ($value->toHTML());
			break;

		case 'RequestText':
			print ("\t<span class=\"question\">\n");
			print ("\t" . $value->toHTML() . "\n");
			print ("\t</span>\n");
			break;

		case 'HeadingText':
			print ('<h' . ($value->sublevel+1) . '>' . $value->message . '</h' . ($value->sublevel+1) . '>');
			break;

		case 'AlertText':
			print ("\t<span class=\"alert\">\n");
			print ("\t" . $value->toHTML() . "\n");
			print ("\t</span>\n");
			break;

		case 'EditBox':
			disp_editbox($value);
			break;

		case 'PlanContent':
			disp_plan($value);
			break;

		case 'WidgetGroup':
			foreach($value->contents as $widg) {
				disp_widget($widg, null);
			}
			break;
		case 'WidgetList':
			// treat the alphabet on the autoread edit page differently
			if ($value->identifier == 'autoread_alphabet') {
				foreach($value->contents as $widg) {
					disp_widget($widg, null);
				}
				break;
			}
			print ("\n<table" . get_id_or_class($widget) . ">");
			foreach($value->contents as $widg) {
				print ("\n<tr><td>");
				disp_widget($widg, null);
				print ("</td></tr>");
			}
			print ("\n</table>\n");
			break;

		default:
			//foobar
			break;
		}
}

/**
 * Get the string of id and/or class properties for a widget,
 * depending on its settings.
 *
 * @param Widget
 * @return string
 * @todo move this somewhere common
 */
function get_id_or_class($widget) {
	$id = $widget->identifier;
	$class = $widget->group;
	$str = '';
	if ($id) {
		$str .= " id='$id'";
	}
	if ($class) {
		$str .= " class='$class'";
	}
	return $str;
}

function disp_footer($footer) 
{
	//TODO
	if ($footer->doyouread) {
		echo "Do you read " . $footer->doyouread->toHTML() . ", who just updated?<hr>\t<hr>\n";
	}
	echo "\n\n\t<p style=\"font-size: 80%\">\n\n";
	if ($footer->legal) {
		echo "" . $footer->legal->toHTML() . "\n";
	}
}
function disp_mainpanel($page) 
{
?>
<tr>
<td valign="top" align="left" class="left" width="12%">
<table class="mainpanel"><tr><td>
<?php
	$panel = $page->mainpanel;
	// print the logo
	echo "<p class=\"logo\">&nbsp;</p>";
	// print the finger form
	if ($panel->fingerbox) {
?>

<Form action="<?php echo $panel->fingerbox->action
?>" method="<?php echo $panel->fingerbox->method
?>">
<?php
		foreach($panel->fingerbox->contents as $item) {
			print ($item->toHTML() . "\n");
			if ($item->name == "searchname") echo "<br>\n";
		}
?></form>
<table class="lowerpanel">
<?php
	}
	// print the user's links
	if ($panel->requiredlinks) disp_links($panel);
	// print the autoread
	if ($panel->autoreads) disp_autoread($panel->autoreads, $page->url, $page->autoreadpriority);
?>




</table>
</td></tr></table>
</td>
<?php
}
function disp_links($panel) 
{
	$front = "\n<tr>\n<td><p class=\"imagelev1\">&nbsp;</p></td><td></td><td></td>\n<td>";
	$end = "</td>\n</tr>\n"; // TODO do we want a class div around these?
	// print out the links
	if ($panel->requiredlinks) for ($i = 0; ($l = $panel->requiredlinks[$i]); $i++) {
		$l->html_attributes = ' class="main"';
		$l->description = strtolower($l->description); //TODO necessary?
		if (strtolower($l->description) == 'log out') $logout = $l;
		else echo $front . $l->toHTML() . $end;
	}
	if ($panel->optionallinks) for ($i = 0; ($l = $panel->optionallinks[$i]); $i++) {
		$l->html_attributes = ' class="main"';
		echo $front . $l->toHTML() . $end;
	}
	if ($logout) echo $front . $logout->toHTML() . $end;
?>
<tr><td><br></td><td><br></td><td><br></td><td><br></td></tr>

<?php
}
function disp_autoread($autoreads, $myurl, $privl) 
{
?>
<tr><td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>

<td><p class="main">auto read list</p></td>
</tr>

<?php
	echo "</table>\n";
	echo "<table>\n";
	$front = "<tr><td></td><td></td><td><p class=\"imagelev3\">&nbsp;</p></td>" . "\n<td>";
	$end = "</td></tr>\n";
	foreach($autoreads as $ar) {
                $priority = $ar->priority;
                $new_url = "setpriv.php";
		$new_url = add_param($new_url, 'myprivl', $priority);
		echo '<tr><td></td><td><p class="imagelev2' . '">&nbsp;</p></td><td></td>' . "\n";
		echo '<td><a href="' . $new_url . '" class="lev2' . '">level ' . $priority . '</a>' . "\n</td>\n";
		// If this is the current autoread level
		if ($priority == $privl) {
			// Print the clear button
			echo '<td><a onClick =" ' . " return confirm('Are you sure you\'d like to mark all the Plans on level " . $priority . " as read?')" . '" href="setpriv.php?mark_as_read=1">X</a></td></tr>' . "\n";
			// now print the plans on this autoread level
			foreach($ar->contents as $item) {
				$item->html_attributes = ' class="lev3"';
				echo $front . $item->toHTML() . $end;
			}
		} else {
			echo "</tr>";
		}
	}
}
/*
?>
<?

}


function priority_link($myurl, $notprivl)
{

if (ereg("myprivl", $myurl))
{$myurlx = ereg_replace("myprivl=[0-9]{0,1}", "myprivl=" . $notprivl,
$myurl);
}//if already has privl
else { //if doesn't already have privl
if (ereg("\?", $myurl)) // if has ? but not privl
{$myurlx = $myurl . "&myprivl=" . $notprivl;}
else  //must add on extra info
{$myurlx= $myurl . "?myprivl=" . $notprivl;}
}//else, if doesn't already have privl

echo "<tr><td></td><td><p class=\"imagelev2\">&nbsp;</p></td><td></td>";
echo "<td><a href=\"http://" . $myurlx . "\" class=\"lev2\">level " .
$notprivl .
"</a></td></tr>";
}//function priority_link



function mdisp_end($dbh,$idcookie,$myurl,$myprivl)
{echo "</td></tr></table></td></tr></table>";
disclaimer();
echo "</body></html>";}

*/
function disp_plan($plan) 
{
?>
<table><tr><td><p class="main">Username: </p></td><td><b><?php echo $plan->username
?></b></td></tr></table><table><tr><td><p class="main2">Last login: </p></td><td><?php echo $plan->lastlogin
?></td></tr></table><table><tr><td><p class="main3">Updated on: </p></td><td><?php echo $plan->lastupdate
?></td></tr></table><table><tr><td><p class="main4">Name:</p></td><td><u><?php echo $plan->planname
?></u></td></tr></table><p class="sub">
<?php
	print ($plan->text);
?>
	</p>
<?php
	if ($plan->addform) {
		echo "<BR><BR><BR><BR><BR>";
		disp_widget($plan->addform, NULL);
	}
}
function disp_editbox($box) 
{
?>
		<form action="<?php echo $box->action
?>" method="<?php echo $box->method
?>" id="editform" name="editform">
		<textarea rows="<?php echo $box->rows
?>" cols="<?php echo $box->columns
?>" 
		name="plan" wrap="virtual" onkeyup="javascript:countlen();">
<?php
	print ($box->text->message);
?>
		</textarea><input type="hidden" name="part" value="1"><br>
		<img src="left.gif" width="2" height="16"><img id="filled" src="filled.gif" width="0" height="16"><img id="unfilled" src="unfilled.gif" width="100" height="16"><img src="right.gif" width="2" height="16"> <input type="text" name="perc" value="0%" size="4" style="border: 0px" readonly>
		&nbsp;&nbsp;&nbsp;<input type="submit" value="Change Plan">
	</form>

<?php
	/*
	</textarea><input type="hidden" name="part" value="1">
	<input type="hidden" name="myprivl" value="" . $myprivl . "">
	<span id="filled_left"></span><span id="filled"><span id="unfilled"></span><span id="filled_right">
	<input id="filled_percent" type="text" name="perc" value="0%" readonly>
	<input id="submitplan" type="submit" value="Change Plan"></form>
	*/
?>
<?php
}
?>
