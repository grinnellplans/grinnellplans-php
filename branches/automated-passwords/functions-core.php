<?php
require_once('Plans.php');
require_once ('dbfunctions.php');

function isvaliduser($dbh, $username)
{
        if (!get_items($dbh, "username", "accounts", "username", $username)) {
                return 0;
        } else {
                return 1;
        }
}

function isValidEmail($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);  
}  
?>
