<?

require_once("cookie_session.php");
require("functions-main.php");//load main functions
$idcookie = $_SESSION['userid']; 
$userid = $idcookie;
$auth = $_SESSION['is_logged_in'];


$dbh = db_connect();//connect to database

$myprivl=setpriv($myprivl, $HTTP_COOKIE_VARS["thepriv"]);

if ($auth) {
	mdisp_begin($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl);
} else { 
	gdisp_begin($dbh);
}
if (!$auth) {
	echo "Please log in";
} else {
	$poll_question_id = $_GET['poll_question_id'];
	$submitted = $_GET['submitted'];
	if (! $poll_question_id) {
		$sql = "select max(poll_question_id) as max from poll_questions; ";
		$res = mysql_query($sql);
		$new_row = mysql_fetch_array($res);
		$poll_question_id = $new_row['max'];
	}
	

	$responses = array();
	$sql = "select type, q.html as question from poll_questions q 
	where poll_question_id = $poll_question_id";
	$res = mysql_query($sql);
	$new_row = mysql_fetch_array($res);
	$question = $new_row['question'];
	$type = $new_row['type'];

	if ($submitted) {
		$poll_choice_ids = array();
		$poll_choice_id = $_GET['poll_choice_id'];
		if (! isset($poll_choice_id)) {
			$poll_choice_ids = array();
		} else {

			if ($type == 'single') {
				if (is_array($poll_choice_id)) {
					$poll_choice_ids[] = $poll_choice_id[0];
				} else {
					$poll_choice_ids[] = $poll_choice_id;
				}
			} else {
				$poll_choice_ids = $poll_choice_id;
			}
		}

		$sql = "delete poll_votes from poll_votes join poll_choices using (poll_choice_id) 
		where userid = $userid and poll_question_id = $poll_question_id ";
		mysql_query($sql);
		foreach ($poll_choice_ids as $poll_choice_id) {
			$sql = "insert into poll_votes set userid = $userid,
			created = now(),
			poll_choice_id = $poll_choice_id";
			mysql_query($sql);
		}
	}


	echo "<h3><i>$question</i></h3><br />";
	echo '<form method="GET" action="">';
	echo '<table style="text-align:center">';
	echo "<tr><th>Options</th><th>Yes! I want to pick this one</th><th>Votes so far</th></tr>\n";
	$sql = "select c.html as html, c.poll_choice_id as poll_choice_id, v.userid as checked from poll_choices c left join poll_votes v on v.userid = $userid and v.poll_choice_id = c.poll_choice_id where c.poll_question_id = $poll_question_id order by c.html";
	$html_res = mysql_query($sql);
	while ($new_row = mysql_fetch_array($html_res)) {
		$html = $new_row['html'];
		$checked = $new_row['checked'];
		$poll_choice_id = $new_row['poll_choice_id'];
		$html = $new_row['html'];
		echo "<tr><td>$html</td>\n<td>";
		if ($checked) {
		$checked = "Checked";
			} else { 
				$checked = '';
				}
		if ($type == 'single') {
			echo '<input type="radio" name="poll_choice_id" value="' . $poll_choice_id . '" ' . $checked . ' ></td><td>';
		} else {
			echo '<input type="checkbox" name="poll_choice_id[]" value="' . $poll_choice_id . '" ' . $checked . ' ></td><td>';
		}
		$sql = "select count(*) as popularity from poll_votes v
		where v.poll_choice_id = $poll_choice_id";
		$res = mysql_query($sql);
		$new_row = mysql_fetch_array($res);
		$popularity = $new_row['popularity'];
		echo "$popularity";
		echo "</td></tr>\n";
	}
	echo "</table>\n";
	echo '<input type="hidden" name="poll_question_id" value="' . $poll_question_id . '">';
	echo '<input type="hidden" name="submitted" value="1">';
	echo '<input type="submit" value="Vote!">';
	echo "</form>\n";
	$sql = "select count(*) as voted from poll_votes v join poll_choices c using (poll_choice_id) where userid = $userid and poll_question_id = $poll_question_id";
	$res = mysql_query($sql);
	$new_row = mysql_fetch_array($res);
	$voted = $new_row['voted'];
	if ($voted) {
		echo "<br />You have voted in this poll, but you may change your mind.<br/>\n";
	} else {

		echo "<br /><br/>\n";
	}
}
  ?>
  <p> <b>All Polls</b>


<?php
	list_polls();
	?>

	<p>
<span style="font-size:.7em"> Poll ideas?  <a href="mailto:grinnellplans@gmail.com">Email</a>.  </span>
	</p>
	<?php
  if ($auth)
    {
      mdisp_end($dbh,$idcookie,$HTTP_HOST . $REQUEST_URI,$myprivl); //and send closing display data
    }
  else
    {gdisp_end();}//if guest send guest closing display data

	?>
  </p>

  </body>
  </html>


  <?php

	  function list_polls() {
		  $sql = "select html, poll_question_id from poll_questions where poll_question_id not in (16, 17) order by poll_question_id desc";
		  $res = mysql_query($sql);
		  echo '<table>';
		  while($new_row = mysql_fetch_array($res)) {
			  echo '<tr><td>';
			  echo ' <a href="?poll_question_id=';
			  echo $new_row['poll_question_id'];
			  echo '">';
			  echo preg_replace('/<[^>]*>/', '', $new_row['html']);
			  echo '</a>';
			  echo '</td></tr>';
			  echo "\n";
		  }
		  echo '</table>';

	  }  
	  ?>
