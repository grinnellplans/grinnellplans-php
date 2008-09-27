<?

session_start();
require("functions-main.php");//load main functions

$dbh = db_connect();//connect to database

$idcookie = $_SESSION['userid']; 
$auth = $_SESSION['is_logged_in'];

$myprivl=setpriv($myprivl, $HTTP_COOKIE_VARS["thepriv"]);


	    if ($auth)//begin valid user display
		{
			mysql_query("delete from viewed_secrets where userid = $idcookie");
			mysql_query("insert into viewed_secrets (userid, date) values($idcookie, now())");
			mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);}
            else //begin guest user display
			{gdisp_begin($dbh);}
       
              ?>


              <p>

             Here you can post anonymously.  This page was created to help give wgemigh his plan back, and to make it possible to paginate an increasing number of secrets.  Please add this page as an optional link under 'preferences'.
             </p><p>
            Secrets cannot be tracked by any Plans administrator.  If you're still worried, you may log out before posting. We can exercise editorial discretion as to what shows up.
	    
                 <a href="#" onClick =" document.getElementById('secrets').style.display = 'block';">Post a Secret </a></p>
              <div id="secrets">
<form method="POST">

<textarea name="secret" rows=10 cols=50>
</textarea>
<input type="hidden" name="secret_submitted" value="1">
<input type="submit" value="Post">
</form>
</div>
<script>
<!-- 
    document.getElementById('secrets').style.display = 'none';
    -->
    </script>  
<?php
              
	if ($_POST['secret_submitted']) {
		$secret = $_POST['secret'];
		$secret = cleanText($secret);
		$sql = "insert into secrets(secret_text, date, display) values (substring('$secret',1,4000), now(), 'no')";
		mysql_query($sql);
	} 

	if ($auth) {

		$count = 300;
		$offset = $_GET['offset'];
		if (! is_numeric($offset)) {
			$offset = 0;
		}

		echo "<!--- $offset --->";


		if ($_SERVER['REMOTE_ADDR'] == '70.12.157.227' || $_GET['show_all']) {
			$select_query = "select * from secrets order by date desc limit $offset, $count";
		} else {
			$select_query = "select * from secrets where display = 'yes' or display = 'pref'  order by date desc limit $offset, $count";
		}

		if (!$secrets =
		mysql_query($select_query))
		{ echo "No secrets";}
		else {
			while ($row = mysql_fetch_array($secrets)) {
				echo '<p class="sub">';
				$secret = $row['secret_text'];
				$date = $row['date'];
				$secretidno = $row['secret_id'];
				echo "$secretidno <b>$date</b><br />\n";
				echo "$secret\n";
			}
			echo '</p>';
		}

	}


	    if ($auth)//begin valid user display
        {
  mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);
} else
{gdisp_end();}

db_disconnect($dbh);

?>
