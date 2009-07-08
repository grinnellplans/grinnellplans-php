<?php
require_once('Plans.php');
require('functions-main.php');
$dbh = db_connect();
//gives a nice blanks form if person changes which priority level they are viewing right after they log in. Since otherwise it would reload the login page and the person would have to log back in again.
$idcookie = User::id();
if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
} else {
	echo "<html><body>Nothing to see here.</body></html>";
}
db_disconnect($dbh);
?>
