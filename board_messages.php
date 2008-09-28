<?php
require_once ("Plans.php");
require ("functions-main.php"); //load main functions
$dbh = db_connect(); //establish the database handler
$messagesperpage = messagesperpage();
$idcookie = $_SESSION['userid'];
$auth = $_SESSION['is_logged_in'];
if (!$auth) {
	gdisp_begin($dbh); //begin guest display
	echo ("You are not allowed to edit as a guest."); //tell person they can't log in
	gdisp_end();
} //end guest display
else
//elseallowed to edit
{
	mdisp_begin($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //begin user display
	
?>

	<script language="JavaScript">
	<!-- 
	function Show_Stuff(Click_Menu)
	{
		if (Click_Menu.style.display == "none")
		{
			Click_Menu.style.display = "";
		}
		else
		{
			Click_Menu.style.display = "none";
		}
	}
        function is_activated(arrow) {
            return (arrow.style.borderWidth);
        }
        function activate(arrow) {
            arrow.style.border = '#222222 thin solid';
        }
        function deactivate(arrow) {
            arrow.style.border = '';
            arrow.style.borderWidth = '';
        }
        function vote(messageid, vote) {
            yes_arrow = document.getElementById(messageid+'y');
            no_arrow = document.getElementById(messageid+'n');
            counter = document.getElementById(messageid+'c');
            num_votes = document.getElementById(messageid+'i');
            if (vote == 'y') {
                if (is_activated(yes_arrow)) {
                    deactivate(yes_arrow);
                    vote = '';
                    counter.innerHTML = parseInt(counter.innerHTML)-1;
                    num_votes.innerHTML = parseInt(num_votes.innerHTML)-1;
                } else {
                   activate(yes_arrow);
                   if (is_activated(no_arrow)) {
                       deactivate(no_arrow);
                       counter.innerHTML = parseInt(counter.innerHTML)+2;
                   } else {
                       counter.innerHTML = parseInt(counter.innerHTML)+1;
                       num_votes.innerHTML = parseInt(num_votes.innerHTML)+1;
                   }
                }
            } else {
                if (is_activated(no_arrow)) {
                    deactivate(no_arrow);
                    vote = '';
                    counter.innerHTML = parseInt(counter.innerHTML)+1;
                    num_votes.innerHTML = parseInt(num_votes.innerHTML)-1;
                } else {
                   activate(no_arrow);
                   if (is_activated(yes_arrow)) {
                       deactivate(yes_arrow);
                       counter.innerHTML = parseInt(counter.innerHTML)-2;
                   } else {
                       counter.innerHTML = parseInt(counter.innerHTML)-1;
                       num_votes.innerHTML = parseInt(num_votes.innerHTML)+1;
                   }
                }
            }
            
            if (window.XMLHttpRequest) {
                request = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            request.open("GET", "vote_thread.php?messageid="+messageid+"&vote="+vote, true);
            request.send(null);
        
        }
	-->
	</script>


	<div style="position: fixed;width: 50px;display:none" id="reply"><a 
	href="javascript:Show_Stuff(display1);javascript:Show_Stuff(hide)" class="lev2">Reply</a></div><br>


	<span id="display1">

	<table>
	<tbody><tr><td wrap="" width="200">

	<form action="board_submit.php" method="POST">
	<input type="hidden" name="checknum" value="<?php
	echo $idcookie
?>">

	<textarea rows="11" cols="50" name="messagecontents" wrap="virtual"></textarea><br>
	<input type="hidden" name="threadid" value="<?php
	echo $_REQUEST['threadid'] ?>">
	<input type="hidden" name="submit" value="1"><input type="submit" value="Submit"></form>
	</td>
	</tr>
	</tbody></table>
	</span>
	<script>
	<!-- 
	document.getElementById('display1').style.display = 'none';
	document.getElementById('display1').style.position = 'fixed';
	document.getElementById('reply').style.display = '';

	-->
	</script> 






	<?php
	if ($messagenum > 0) {
		$messagevals = get_items($dbh, "threadid, created", "subboard", "messageid", $messagenum);
		$threadid = $messagevals[0][0];
		if (!$threadid) {
			echo "The message you requested has been deleted or does not exist.";
			mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //gets user display
			stop();
		}
		$my_result = mysql_query("Select COUNT(*) From subboard WHERE created >= \"" . $messagevals[0][1] . "\" and threadid=\"" . $threadid . "\"");
		$pagefind = mysql_fetch_row($my_result);
		$pagenumber = floor($pagefind[0] / messagesperpage());
	}
	$my_result = mysql_query("Select COUNT(*) From subboard WHERE threadid=\"" . $threadid . "\"");
	$totalmessages = mysql_fetch_row($my_result);
	if (!($pagenumber > 0)) {
		$pagenumber = 0;
	}
	if ($pagenumber > floor($totalmessages[0] / $messagesperpage)) {
		$pagenumber = floor($totalmessages[0] / $messagesperpage);
	}
	echo "<center>";
	if ($pagenumber > 0) {
		echo "<a href=\"board_messages.php?pagenumber=0&threadid=" . $threadid . "\">&lt;&lt;</a> ";
	} else {
		echo "&lt;&lt; ";
	}
	if ($pagenumber >= 2) {
		$tempnum = $pagenumber - 2;
		echo "<a href=\"board_messages.php?pagenumber=" . $tempnum . "&threadid=" . $threadid . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if ($pagenumber >= 1) {
		$tempnum = $pagenumber - 1;
		echo "<a href=\"board_messages.php?pagenumber=" . $tempnum . "&threadid=" . $threadid . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	echo "[" . $pagenumber . "] ";
	if ($totalmessages[0] > ($pagenumber + 1) * $messagesperpage) {
		$tempnum = $pagenumber + 1;
		echo "<a href=\"board_messages.php?pagenumber=" . $tempnum . "&threadid=" . $threadid . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if ($totalmessages[0] > ($pagenumber + 2) * $messagesperpage) {
		$tempnum = $pagenumber + 1;
		echo "<a href=\"board_messages.php?pagenumber=" . $tempnum . "&threadid=" . $threadid . "\">" . $tempnum . "</a> ";
	} else {
		echo "_ ";
	}
	if (floor($totalmessages[0] / $messagesperpage) > $pagenumber) {
		echo "<a href=\"board_messages.php?pagenumber=" . floor($totalmessages[0] / $messagesperpage) . "&threadid=" . $threadid . "\">&gt;&gt;</a></center>";
	} else {
		echo "&gt;&gt;</center>";
	}
	$rowoffset = $messagesperpage * $pagenumber;
	/*
	echo "<table border=\"1\"><tr bgcolor=\"#999999\"><td><center><b>Title</b></center></td><td><center><b>Newest
	Message</b></center></td><td><center><b># of Messages</b></center></td></tr>";
	*/
	echo "\n\n <!-- rowoffset = $rowoffset, messagesperpage = $messagesperpage, pagenumber = $pagenumber -->\n";
	$thread_title = get_item($dbh, "title", "mainboard", "threadid", $threadid);
	echo ' <br /> <p style="font-weight:bold; text-align:center"> ' . stripslashes($thread_title) . " </p> \n";
	echo "<table class=\"boardmessages\">";
	$notes_pref = get_item($dbh, "notes_asc", "accounts", "userid", $userid);
	echo "<!-- $notes_pref -->";
	if ($notes_pref) {
		$rowoffset = $totalmessages[0] - $messagesperpage * ($pagenumber + 1);
		if ($rowoffset < 0) {
			//$messagesperpage = -1 * $rowoffset;
			$rowoffset = 0;
		}
	}
	echo "\n\n <!-- totalmessages[0] = $totalmessages[0], rowoffset = $rowoffset, messagesperpage = $messagesperpage, pagenumber = $pagenumber -->\n";
	//echo "\n\n <!-- " . $messagesperpage * $pagenumber . " --> \n";
	$query = "Select subboard.messageid, 
                DATE_FORMAT(subboard.created, ' %l:%i %p, %a %M %D, %Y'),
                subboard.userid, accounts.username, subboard.title ,subboard.contents, ifnull(vts.votes,0), mv.vote, ifnull(vts.num_votes,0)
                From 
                subboard left join  accounts using (userid)
                left join (select messageid, sum(vote) as votes, count(*) as num_votes from boardvotes 
                           where threadid = " . $threadid . "
                           group by messageid) as vts on 
                vts.messageid = subboard.messageid
                left join (select messageid, vote from boardvotes where userid = " . $idcookie . ") as mv
                     on mv.messageid = subboard.messageid
                where subboard.threadid = " . $threadid . " 
                ORDER BY subboard.messageid DESC 
                LIMIT " . $rowoffset . "," . $messagesperpage;
	$my_result = mysql_query($query);
	$colorlight = "<table class=\"noteslight\" width=\"100%\">";
	$colordark = "<table class=\"notesdark\" width=\"100%\">";
	$colorswitch = 0;
	while ($new_row = mysql_fetch_row($my_result)) {
		if ($colorswitch == 0) {
			$thecolor = $colorlight;
			$colorswitch = 1;
		} else {
			$thecolor = $colordark;
			$colorswitch = 0;
		}
		if ($new_row[3]) {
			$display_planlove = "[<a href=\"read.php?searchname=" . $new_row[3] . "\">" . $new_row[3] . "</a>]";
		} else {
			$display_planlove = "<i>User Deleted</i>";
		}
		$yes_vote = $no_vote = "{}";
		if ($new_row[7] == 1) {
			$yes_vote = "border: #222222 thin solid";
		}
		if ($new_row[7] == - 1) {
			$no_vote = "border: #222222 thin solid";
		}
		echo "<tr><td>";
		echo $thecolor . "<tr><td>";
		echo "<tr><td><b><p id=\"" . $new_row[0] . "\">" . stripslashes($new_row[4]) . "</p></b></td></tr>";
		echo "<tr><td><table border=\"1\" width=\"100%\"><tr><td>" . $new_row[0] . "</td>
    <td style=\"cursor:pointer; cursor:hand\"><span style=\"" . $yes_vote . "\" id=\"" . $new_row[0] . "y\" onclick=\"vote(" . $new_row[0] . ",'y');\">&uarr;</span>&nbsp;&nbsp; <span style=\"" . $no_vote . "\" id=\"" . $new_row[0] . "n\" onclick=\"vote(" . $new_row[0] . ",'n');\">&darr;</span> &nbsp;&nbsp;(<span id=\"" . $new_row[0] . "c\">" . $new_row[6] . "</span>) (<span id=\"" . $new_row[0] . "i\">" . $new_row[8] . "</span> votes)</td>
<td>" . $new_row[1] . "</td><td><center>" . $display_planlove . " </center></td></tr></table></td></tr>";
		echo "<tr><td>" . stripslashes($new_row[5]) . "<br><br></td></tr>";
		echo "</table>";
		echo "</td></tr>";
	}
	echo "</table>";
	mdisp_end($dbh, $idcookie, $HTTP_HOST . $REQUEST_URI, $myprivl); //gets user display
	
}
db_disconnect($dbh);
?>
