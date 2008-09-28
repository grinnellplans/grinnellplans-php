<?php
	require_once("Plans.inc");
function insert_user($added_name, $password, $gradyear, $email, $type, $status = '') {
	if (!$password) {
		srand(time());
		$password = rand(0,999999);
	}
	if (!$email) {
		$email = $added_name . "@grinnell.edu";
	}
	$crpassword = crypt($password, "ab");
	$myrow = array("",$added_name,"",$crpassword,$email,"","","","","","","",$gradyear,"70","14","", "", "", $type, "", "");
	add_row($dbh, "accounts", $myrow);

	mysql_query("UPDATE accounts SET created = NOW() WHERE
			username = '$added_name'");

	$added_id = get_item($dbh,"userid","accounts","username", $added_name);
	$myrow = array($added_id,"1","2");
	add_row($dbh, "display", $myrow);

	foreach (array(2,4,6,8,14,15,16) as $opt_link) {
		$myrow = array($added_id,$opt_link);
		add_row($dbh, "opt_links", $myrow);
	}
	$myrow = array($added_id,$status);
	add_row($dbh, "perms", $myrow);



	return array($password, $email);
}
		?>
