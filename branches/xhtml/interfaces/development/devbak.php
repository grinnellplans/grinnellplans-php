<?php
function disp_begin($dbh, $idcookie, $myurl, $myprivl, $cssloc, $jsfile)
{
	if (!$myprivl == 2 or !$myprivl == 3) {
		$myprivl = 1;
	}
	$searchname = $_GET['searchname'];
	if ($searchname) {
		$title = "[$searchname]'s Plan";
	} else {
		$title = "Plans Beta";
	}
?>
<html>
<head>
<title><?php
	echo $title ?></title>
<link rel=stylesheet
href="<?php echo $cssloc ?>">
<?php
	if (!is_null($jsfile)) echo "<script language=\"javascript\" type=\"text/javascript\" src=\"$jsfile\"></script>";
?>
</head>
<body>

<table width="100%" cellspacing="0" cellpadding="0"
class="main">
<tr>
<td valign="top" align="left" class="left" width="12%">
<table class="mainpanel"><tr><td>
<p class="logo">&nbsp;</p>
<Form action="read.php" method="get">
<input name="searchname" type="text"><br>
<input type="hidden" name="myprivl" value="<?php
	echo $myprivl; ?>">
<input type="submit" value="Read"></form>

<table class="lowerpanel"><tr>
<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><a href="edit.php?myprivl=<?php
	echo $myprivl; ?>" class="main">edit 
plan</a></td>
</tr>


<tr>
<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><a href="listusers.php?myprivl=<?php
	echo $myprivl; ?>" class="main">list 
users</a></td>
</tr>

<tr>
<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><a href="search.php?myprivl=<?php
	echo $myprivl; ?>"
class="main">search plans</a></td>
</tr>

<tr>
<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><a href="customize.php?myprivl=<?php
	echo $myprivl; ?>"
class="main">preferences</a></td>
</tr>
<?php
	$buf = '<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>';
	show_opt_links($idcookie, $buf);
?>

<tr>
<td><p class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><a href="index.php?logout=1" class="main">log out</a></td>
</tr>

<tr><td><br></td><td><br></td><td><br></td><td><br></td></tr>

</table>
</td></tr></table>
</td>
<td valign="top">

<br />
<table>

<tr><td>

<?php
}
function mdisp_end($dbh, $idcookie, $myurl, $myprivl)
{
	if (!$myprivl == 2 and !$myprivl == 3) {
		$myprivl = 1;
	}
?>
</td></tr></table></td><td 
valign="top" align="left" class="right" 
width="22%">
<table class="mainpanel"><tr><td>
<table class="lowerpanel">

<tr><td><p 
class="imagelev1">&nbsp;</p></td><td></td><td></td>
<td><p class="main">auto read list</p></td>
</tr>

<?php
	autoread_list($myurl, $idcookie, $myprivl);
?>
</table>
</td></tr></table>
</td></tr></table>
<?php
	disclaimer();
?>

</body></html>
<?php
}
?>
