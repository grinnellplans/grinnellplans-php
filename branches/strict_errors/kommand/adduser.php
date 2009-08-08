<?php
require_once ("../Plans.php");

$added_name = $_POST['username'];
require ("auth.php");
require ("../functions-kommand.php");
?>
<html>
<body>
<?php
require ("dbfunctions.php");
$dbh = db_connect();
if ($added_name) {
	if (isvaliduser($dbh, $added_name)) {
		echo "User already exists.";
	} else {
		$type = $_POST['type'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		$perms = $_POST['perms'];
		$gradyear = $_POST['gradyear'];

		if ($type == "other") {
			$type = $_POST['other'];
		}
		$results = insert_user($added_name, $password, $gradyear, $email, $type, $perms);
		$password = $results[0];
		$email = $results[1];
		echo "Account created for " . $added_name . " with password " . $password . ", email " . $email . ", and graduation year " . $gradyear . ".<br>";
?>
		<form action="email.php" method="POST">
		<input type="hidden" name="username" value="<?php echo $added_name
?>">
		<input type="hidden" name="password" value="<?php echo $password
?>">
		<input type="hidden" name="email" value="<?php echo $email
?>">
		<input type="hidden" name="type" value="<?php echo $type
?>">
		<input type="hidden" name="perms" value="<?php echo $perms
?>">
		<input type="hidden" name="whatoperation" value="create">
		<input type="submit" value="Send Email">
		</form>
		<?php
	}
} else {
?>
	<form name="signup" action="adduser.php" method="POST">
	Username: <input type="text" name="username"><br>
	Password: <input type="text" name="password"><br>
	E-mail: <input type="text" name="email"><br>
	
		<table>
		<tr><td rowspan="6">
		Relation to Grinnell? 
		</td></tr>
		<tr><td>Student </td><td> <input type="radio" name="type" value="student" onClick ="toggle('year', 0);toggle('other', 4);">
		
<span id="year"> Grad Year: <input type="text" name="gradyear"> </span>
		
		</td></tr>
		<tr><td>Staff  </td><td> <input type="radio" name="type" value="staff" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
		<tr><td>Group  </td><td> <input type="radio" name="type" value="group" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
		<tr><td>Faculty  </td><td> <input type="radio" name="type" value="faculty" onClick ="toggle('year', 0);toggle('other', 4);"></td></tr>
		<tr><td>Other  </td><td> <input type="radio" name="type" value="other" onClick ="toggle('year', 0);toggle('other', 4);"> <span id="other">
Description: <input type="text" name="other" onkeyup="recount_chars()"> <i> max of 128 chars. So far you have typed <span id="char_count"></span></i>
</span>
		
		
		</td></tr>
		</table>   
<p>
	<b>Permissions</b>	<br />
Normal: <input type="radio" name="perms" value=''><br />
Write-Only: <input type="radio" name="perms" value='write-only'>

</p>
	<input type="submit" value="Create Account">
	</form> 
	Guidelines:<br>
	&nbsp; * Students should have a two digit grad year (ie, 2007 -> 07)<br>
	&nbsp; * Group is anything officially sanctioned by SGA<br>
	&nbsp; * Faculty are professors<br>
	&nbsp; * Staff are other people like ITS, FM workers, dining hall staff, etc<br>
	&nbsp; * For city plans, put "city" in Other<br>
	&nbsp; * For RLCs, put "rlc" in other instead of calling them staff, because they're a special case<br>
	&nbsp; * For anything else, put a few essential keywords in the "other"<br>
	&nbsp; * Remember that if a new group of "other" plans comes up, this list can be added to <br>
	<?php
} //if no username
db_disconnect($dbh);
?>
	<script>
	<!-- 
	    document.getElementById('year').style.display = 'none';
	    document.getElementById('other').style.display = 'none';
		recount_chars();


		function recount_chars() {
			document.getElementById("char_count").innerHTML = document.signup.other.value.length;
		}

function toggle(item, box) {
	if ( document.signup.type[box].checked == true) {
		 document.getElementById(item).style.display = 'inline';
	} else {
		 document.getElementById(item).style.display = 'none';
	}
}
		    -->
			    </script>  
</body>
</html>
