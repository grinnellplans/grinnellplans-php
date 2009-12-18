<?php
require_once("Plans.php");
require_once("dbfunctions.php");

function isValidEmail($email) {  
	return filter_var(filter_var($email, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);  
}  
?>
