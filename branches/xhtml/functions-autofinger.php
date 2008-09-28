<?php
require_once ("Plans.php");
//////////
/*
<<<<<<< .working
setpriv - This function sets privilege level
=======
setpriv - This function sets priviledge level
>>>>>>> .merge-right.r91
*/
function setpriv($privl, $cookpriv)
{
	if ($privl) {
		if ($privl != $cookpriv) {
			setcookie("thepriv", $privl, 0, "");
		}
		return $privl;
	} else {
		return $cookpriv;
	}
}
//////////
/*
*Simply sets a plan that's on a person's autoread list to be marked as read
*/
function update_read($dbh, $owner, $updated)
{
	mysql_query("UPDATE autofinger SET updated = '0' WHERE owner = '$owner' and interest = '$updated'");
}
//////////
/*
*Marks when a person reads a plan
*/
function setReadTime($dbh, $idcookie, $interest)
{
	mysql_query("UPDATE autofinger SET readtime = NOW() WHERE owner = $idcookie AND interest = $interest");
}
function mark_as_read($dbh, $owner, $privl)
{
	$query = "UPDATE autofinger set updated = 0 where owner ='$owner' and priority = '$privl'";
	//echo $query;
	mysql_query($query);
	//echo $privl;
}
function add_param($url, $name, $value)
{
	if (ereg($name, $url)) {
		return ereg_replace("$name=[^&]*", $name . '=' . $value, $url);
	} else {
		if (ereg("\?", $url)) {
			return $url . '&' . $name . '=' . $value;
		} else {
			return $url . '?' . $name . '=' . $value;
		}
	}
}
function remove_param($url, $name)
{
	$url = ereg_replace("$name=[^&]*", '', $url);
	$url = preg_replace(array("@&$@"), array(''), $url);
	return $url;
}

//TODO deprecate this shit
function autoread_list($myurl, $idcookie, $myprivl)
{
	//echo '<!-- JLW ' . $myurl . "-->\n\n";
	echo "</table>\n";
	echo "<table>\n";
	$mark_as_read = $_GET['mark_as_read'];
	if ($mark_as_read) {
		//       echo $mark_as_read;
		mark_as_read($dbh, $idcookie, $myprivl);
	}
	for ($priority = 1; $priority < 4; $priority++) {
		$new_url = add_param($myurl, 'myprivl', $priority);
		$new_url = remove_param($new_url, 'mark_as_read');
		echo '<tr><td></td><td><p class="imagelev2' . '">&nbsp;</p></td><td></td>' . "\n";
		echo '<td><a href="http://' . $new_url . '" class="lev2' . '">level ' . $priority . '</a>' . "\n";
		echo '</td>' . "\n";
		if ($priority == $myprivl) {
			$privarray = mysql_query("Select autofinger.interest,accounts.username
			From autofinger, accounts where owner = '$idcookie' and priority =
			'$myprivl' and updated = '1' and autofinger.interest=accounts.userid");
			while ($new_row = mysql_fetch_row($privarray)) {
				$autoreadlist[] = $new_row;
			}
			echo '<td><a onClick =" ' . " return confirm('Are you sure you\'d like to mark all the Plans on level " . $priority . " as read?')" . '" href="http://' . add_param($new_url, 'mark_as_read', 1) . '">X</a></td></tr>' . "\n";
			$o = 0;
			while ($autoreadlist[$o][0]) {
				$read_url = 'read.php';
				$read_url = add_param($read_url, 'myprivl', $myprivl);
				$read_url = add_param($read_url, 'searchname', $autoreadlist[$o][1]);
				echo "<tr><td></td><td></td><td><p class=\"imagelev3\">&nbsp;</p></td>" . "\n";
				echo "<td><a href=\"" . $read_url . "\" class=\"lev3\">" . "\n" . $autoreadlist[$o][1] . "</a></td></tr>\n";
				$o++;
			}
			//echo '&nbsp;&nbsp;&nbsp;&nbsp;';
			
		}
	}
}
?>

