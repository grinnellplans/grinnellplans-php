<?php
	require_once("Plans.php");


require("functions-main.php");//load main functions
$dbh = db_connect();//set up database connections

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ( ! $auth) 
{
	gdisp_begin($dbh);//begin guest display
	echo("You are not allowed to edit as a guest.");//tell user they can't edit
	gdisp_end();//end guest display
}
else //allowed to edit
{

	if ($submit)//if form has been submitted
	{

		//if list of available links gets too long, may have to add code in to parse
		//lists so only adding or deleting changing stuff, but for now easier to just
		//delete all and start again
		delete_item($dbh, "opt_links", "userid", $idcookie);//delete current links

		if (count($mylinks))//if there are any links, add them
		{//if values to add
		while(list ($key, $items) = each($mylinks))//for each link the user wants to add, do the loop
		{
			echo "<!-- $idcookie, $key, $items   " . " --> " ;
			$myrow = array($idcookie,$items);//set array to add to database
			add_row($dbh, "opt_links", $myrow);//add new row in database
		}
	}//added values if any

	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//begin valid user display, done later in the page than usual so that the changes will take affect before page is displayed
	echo "<center><h2>Optional Links</h2></center>";
	echo "Optional links changed.";

}//if submit


else //give form
{

	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl); //begin valid user display
	echo "<center><h2>Optional Links</h2></center>";
	$selected_links = get_items($dbh,"linknum","opt_links","userid", $idcookie);//get the current set of links that the user has selected

	$o=0;
	while ($selected_links[$o][0])
	{
		$myselected[$selected_links[$o][0]]=" checked"; //set up so current links will show up in form as checked
		$o++;
	}

	$my_result = mysql_query("Select linknum,linkname,descr From
	avail_links");//get the info on the currently available links
	while($new_row = mysql_fetch_row($my_result)) {
		$all_links[] = $new_row;//get info fron query
	}

	$o=0;
	echo "<form action=\"links.php\" method=\"POST\">";//start form
	while ($all_links[$o][0])
	{
		//display each link
		echo "<input type=\"checkbox\" name=\"mylinks[]\" value=\"" .
		$all_links[$o][0] . "\"" .
		$myselected[$all_links[$o][0]] . ">";
		echo "<b>" . $all_links[$o][1] . "</b><Br>" . $all_links[$o][2] .
		"<BR><BR>";
		$o++;}
		echo "<input type=\"hidden\" name=\"submit\" value=\"1\"><center><input
		type=\"submit\"></center></form>";
	}

	mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);//end valid user display
}//if is a valid user

db_disconnect($dbh);

?>
