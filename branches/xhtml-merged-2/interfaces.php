<?php
require_once('Plans.php');		 
new SessionBroker();		 
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$dbh = db_connect(); //connect to the database
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'interfaces', PLANSVNAME . ' - Interfaces', 'interfaces.php');
if (!User::logged_in()) {
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
	$thispage->append($denied);
} //end guest display
else
//allowed to edit
{
	populate_page($thispage, $dbh, $idcookie);

	$heading = new HeadingText('Interface Options', 2);
	$thispage->append($heading);

	if ($part) //if form has been submitted
	{
		set_item($dbh, "display", "interface", $interface, "userid", $idcookie); //set which interface they selected

		//Let user know the interface has been set
		$message = new InfoText('Interface set.', 'Success');
		$thispage->append($message);
		
	} else
	//if not submitted, give form
	{
		$my_result = mysql_query("Select interface,descr From 
interface"); //get the current interfaces and descriptions
		while ($new_row = mysql_fetch_row($my_result)) {
			$myinterfaces[] = $new_row;
		}
		$intcheck[get_item($dbh, "interface", "display", userid, $idcookie) ] = " checked"; //get user's current selection, and set it to the index of an array and put value to checked
		//begin the form
		
		$interfaceform = new Form('interfacesform', true);
		$thispage->append($interfaceform);

		$item = new HiddenInput('part', 1);
		$interfaceform->append($item);

		$o = 0;
		while ($myinterfaces[$o][0]) //loop through the options
		{
			$item = new RadioInput('interface', $myinterfaces[$o][0]);
			$item->checked = ($intcheck[$myinterfaces[$o][0]] == ' checked');
			$item->description = $myinterfaces[$o][1];
			$interfaceform->append($item);
			$o++;
		}
		$item = new SubmitInput('Change');
		$interfaceform->append($item);
		//end form
		
	}
	
} //if is a valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>
