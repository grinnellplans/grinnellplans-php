<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect(); //establish the database handler
$idcookie = User::id();

if (!User::logged_in()) {
	gdisp_begin($dbh); //begin guest display
	echo ("You are not allowed to edit as a guest."); //tell person they can't log in
	gdisp_end();
} else {
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin user display
	echo "<center><a href=\"board_submit.php?newthread=1\" class=\"lev2\">New Thread</a>";
	$my_result = mysql_query("Select COUNT(*) From mainboard");
	$totalthreads = mysql_fetch_row($my_result);
	$max_page = ceil($totalthreads[0] / NOTES_THREADS_PER_PAGE) - 1;
	if (!($pagenumber > 0)) {
		$pagenumber = 0;
	}
	if ($pagenumber > $max_page) {
		$pagenumber = $max_page;
	}
	if ($pagenumber > 0) {
		echo "<a href=\"board_show.php?pagenumber=0\">&lt;&lt;</a> ";
	} else {
		echo "&lt;&lt; ";
	}
	if ($pagenumber >= 2) {
		$tempnum = $pagenumber - 2;
		echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if ($pagenumber >= 1) {
		$tempnum = $pagenumber - 1;
		echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	echo "[" . $pagenumber . "] ";
	if ($pagenumber <= $max_page - 1) {
		$tempnum = $pagenumber + 1;
		echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if ($pagenumber <= $max_page - 2) {
		$tempnum = $pagenumber + 2;
		echo "<a href=\"board_show.php?pagenumber=" . $tempnum . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if ($max_page > $pagenumber) {
		echo "<a href=\"board_show.php?pagenumber=" . $max_page . "\">&gt;&gt;</a></center>";
	} else {
		echo "&gt;&gt;</center>";
	}
	$rowoffset = NOTES_THREADS_PER_PAGE * $pagenumber;
	echo "<table class=\"boardshow\"><tr class=\"boardrow1\"><td><center><b>Title</b></center></td><td><center><b>Newest 
		Message</b></center></td><td><center><b># Posts</b></center></td><td><center><b>First</b></center></td><td><center><b>Last</b></center></td></tr>";
	$the_query = "Select
mainboard.threadid,
mainboard.title,
DATE_FORMAT(mainboard.lastupdated, '%a %b %D, %l:%i %p'),
count(*),
accounts.username,
maxes.username
From
mainboard
left join (select username, tids.threadid from (select max(messageid) uid, subboard.threadid from subboard, accounts, (select threadid from mainboard order by lastupdated desc limit " . $rowoffset . "," . NOTES_THREADS_PER_PAGE . ") these where subboard.userid = accounts.userid and subboard.threadid = these.threadid group by subboard.threadid) tids, subboard, accounts where tids.uid = subboard.messageid and subboard.userid = accounts.userid) maxes
using
(threadid)
left join
subboard
using
(threadid)
left join
accounts
on mainboard.userid =
accounts.userid
GROUP
BY
threadid
ORDER
BY
lastupdated
DESC
LIMIT " . $rowoffset . "," . NOTES_THREADS_PER_PAGE;
	$my_result = mysql_query($the_query);
	//error_log($the_query);
	$colorswitch = 0;
	while ($new_row = mysql_fetch_row($my_result)) {
		if ($new_row[4]) {
			$display_planlove = "[<a href=\"read.php?searchname=" . $new_row[4] . "\">" . $new_row[4] . "</a>]";
		} else {
			$display_planlove = "<i>User Deleted</i>";
		}
		if ($new_row[5]) {
			$display_planlove2 = "[<a href=\"read.php?searchname=" . $new_row[5] . "\">" . $new_row[5] . "</a>]";
		} else {
			$display_planlove2 = "<i>User Deleted</i>";
		}
		if ($colorswitch == 0) {
			echo "<tr class=\"noteslight\">";
			$colorswitch = 1;
		} else {
			echo "<tr class=\"notesdark\">";
			$colorswitch = 0;
		}
		echo "<td><a href=\"board_messages.php?threadid=" . $new_row[0] . "\">" . stripslashes($new_row[1]) . "</a></td><td>" . $new_row[2] . "</td><td><center>" . $new_row[3] . "</center></td><td>" . $display_planlove . "</td><td>" . $display_planlove2 . "</td></tr>";
	}
	echo "</table>";
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //gets user display
	
}
db_disconnect($dbh);
?>
