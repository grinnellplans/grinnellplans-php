<?php
require_once('Plans.php');
new SessionBroker();

require ('functions-main.php');
require ('syntax-classes.php');

$dbh = db_connect(); //set up database connections
$idcookie = User::id();
$thispage = new PlansPage('Preferences', 'optional_links', PLANSVNAME . ' - Optional Links', 'links.php');

if (!User::logged_in()) {
	populate_guest_page($thispage);
	$denied = new AlertText('You are not allowed to edit as a guest.', 'Access Denied');
	$thispage->append($denied);
} else {
	$title = new HeadingText('Optional Links', 1);
	$thispage->append($title);

	if ($submit) //if form has been submitted
	{
		//if list of available links gets too long, may have to add code in to parse
		//lists so only adding or deleting changing stuff, but for now easier to just
		//delete all and start again
		delete_item($dbh, "opt_links", "userid", $idcookie); //delete current links
		if (count($mylinks)) //if there are any links, add them
		{ //if values to add
			while (list($key, $items) = each($mylinks)) //for each link the user wants to add, do the loop
			{
				$myrow = array($idcookie, $items); //set array to add to database
				add_row($dbh, "opt_links", $myrow); //add new row in database
				
			}
		} //added values if any
		//begin valid user display, done later in the page than usual so that the changes will take affect before page is displayed
		populate_page($thispage, $dbh, $idcookie);
		$thispage->append(new InfoText('Optional links changed.', 'Success'));
	} //if submit
	else
	//give form
	{
		populate_page($thispage, $dbh, $idcookie);

		$selected_links = get_items($dbh, "linknum", "opt_links", "userid", $idcookie); //get the current set of links that the user has selected
		$o = 0;
		while ($selected_links[$o][0]) {
			$myselected[$selected_links[$o][0]] = " checked"; //set up so current links will show up in form as checked
			$o++;
		}
		$my_result = mysql_query("Select linknum,linkname,descr From
	avail_links"); //get the info on the currently available links
		while ($new_row = mysql_fetch_row($my_result)) {
			$all_links[] = $new_row; //get info fron query
			
		}
		$o = 0;
		$linksform = new Form('optionallinks', true);
		$thispage->append($linksform);
		while ($all_links[$o][0]) {
			//display each link
			$item = new CheckboxInput('mylinks[]', $all_links[$o][0]);
			$item->checked = (strtolower(trim($myselected[$all_links[$o][0]])) == 'checked');
			$item->title = $all_links[$o][1];
			$item->description = $all_links[$o][2];
			$linksform->append($item);
			$o++;
		}
		$item = new HiddenInput('submit', 1);
		$linksform->append($item);
		$item = new SubmitInput('Submit');
		$linksform->append($item);
	}
	
} //if is a valid user
interface_disp_page($thispage);
db_disconnect($dbh);
?>
