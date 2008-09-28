<?php
session_start();
$username = $_POST['username'];
require ("auth.php");
?>

<?php
echo '<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
?>
<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
?>

<html>
  <head><title>Enter donations</title></head>
  <body>
    <form method="post" action="/donations/donations.php">
      <table>
	<tr>
	  <td>Name: </td>
	  <td> <input type="text" name="donor_name" /></td>
	</tr>
	<tr>
	  <td>Amount: </td>
	  <td><input type="text" name="amount_added" /></td>
	</tr>
	<tr>
	  <td>Date: </td>
	  <td><input type="text" name="date" /> </td>
	  <td>Format: yyyy-mm-dd<br />
	    <i>Leave blank for current date</i></td>
	</tr>
	<tr>
	  <td>Comments: </td>
	  <td><input type="text" name="comments_added" /></td>
	</tr>
	<tr>
	  <td></td>
	  <td><input type="submit"></td>
	</tr>
      </table>
    </form>
    
    <p>
      <a href="http://validator.w3.org/check/referer"><img
	  src="http://www.w3.org/Icons/valid-xhtml11"
	  alt="Valid XHTML 1.1!" height="31" width="88" /></a>
    </p>
    
    <p>
      Created: Friday, July 25
    </p>
    
  </body>
</html>

<?php
db_disconnect($dbh);
?>

