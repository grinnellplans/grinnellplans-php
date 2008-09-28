<?php
	require_once("Plans.php");

//////////

/*
 *Check to see if is a valid user, displays boolean 1 or 0.
 */

function isvaliduser($dbh, $username)
{
	if
	(!get_items($mydbh,"username","accounts","username",$username)) {return 0;} 
	else {return 1;}
}

//////////

/*
 * Check to see if it is a valid userid/password combination, returns
 * boolean true if valid
 */

function isvalidauth($dbh, $idcookie, $password)
{
	if ( !$idcookie || !$password )
	return( 0 );
	//$password = crypt($password, "ab"); //encrypt the password, change to ab
	$read_pass = get_item($dbh, "password", "accounts", "userid", $idcookie);
	return( $read_pass && $read_pass == $password );
}

?>
