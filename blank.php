<?
	require_once("Plans.php");
require_once("cookie_session.php");
  require("functions-main.php");//load main functions

  $dbh = db_connect();

//gives a nice blanks form if person changes which priority level they are viewing right after they log in. Since otherwise it would reload the login page and the person would have to log back in again.

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

if ($auth)
{mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);
mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);}
else
{ echo "<html><body>Nothing to see here.</body></html>";
}

db_disconnect($dbh);

?>
