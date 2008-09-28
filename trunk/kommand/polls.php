<?php
require_once ("../cookie_session.php");
$username = $_POST['username'];
require ("auth.php");
?>

<?php
require ("dbfunctions.php");
$dbh = db_connect();
$poll_question_id = $_GET['poll_question_id'];
$submitted = $_GET['submitted'];
$html = $_GET['html'];
$type = $_GET['type'];
$responses = fetch_responses();
//print_r($responses);
$response_count = 3;
$first_view;
if ($poll_question_id) {
	//echo "Yes";
	if ($submitted) {
		//update
		$sql = "update poll_questions set html = '$html',
												type = '$type',
												created = now()
											where poll_question_id = $poll_question_id";
		//echo $sql;
		mysql_query($sql);
		foreach($responses['new'] as $response) {
			$sql = "insert into poll_choices set html = '$response',
												poll_question_id = '$poll_question_id',
												created = now()";
			//echo $sql;
			mysql_query($sql);
		}
		foreach(array_keys($responses['updated']) as $poll_choice_id) {
			$new_response = $responses['updated'][$poll_choice_id];
			$sql = "update poll_choices set html = '$new_response'
											where poll_choice_id = '$poll_choice_id'";
			//echo $sql;
			mysql_query($sql);
		}
		foreach($responses['deleted'] as $poll_choice_id) {
			$sql = "delete from poll_choices where poll_choice_id = $poll_choice_id";
			//echo $sql;
			mysql_query($sql);
		}
	}
	//populate values
	//mysql_query("select * from poll_question where
	
} else {
	//echo "No";
	if ($submitted) {
		// echo "Yes";
		//create
		$sql = "insert into poll_questions set html = '$html',
												type = '$type',
												created = now()";
		echo $sql;
		mysql_query($sql);
		$poll_question_id = mysql_insert_id();
		foreach($responses['new'] as $response) {
			$sql = "insert into poll_choices set html = '$response',
												poll_question_id = '$poll_question_id',
												created = now()";
			echo $sql;
			mysql_query($sql);
		}
	} else {
		$first_view = 1;
		//first view, values should be empty.
		
	}
}
if ($poll_question_id) {
	$responses = array();
	$sql = "select type, q.html as question, c.html as choice, 
	c.poll_choice_id as poll_choice_id from poll_questions q
	left join poll_choices c using (poll_question_id) 
	where q.poll_question_id = $poll_question_id order by c.html";
	//echo $sql;
	$res = mysql_query($sql);
	while ($new_row = mysql_fetch_array($res)) {
		$responses[$new_row['poll_choice_id']] = $new_row['choice'];
		$html = $new_row['question'];
		$type = $new_row['type'];
	}
	$response_count = count($responses);
}
?>


<html>
<head>
<style>
p.input_type {
	background-color:#6f6;
}
p.poll_summary {
	background-color:#5f5;
}
</style>
<script>

var mydiv = false;
var div = false;
var response_tick = <?php echo $response_count + 1 ?>;

	function more_answers () {
		mydiv = document.getElementById('answers');
		div = document.createElement("div");
		mydiv.appendChild(div);
		div.innerHTML = 'Response: <textarea rows="2" cols="40" name="new-answer-' 
							+ response_tick + '"></textarea>' + "\n";
		//div.innerHTML += 'Delete: <input type="checkbox" name="delete-' + response_tick + '">';
		response_tick++;
	}
</script>  
</head>
<body>
	<h3> Use this page to create and manage poll questions.</h3>
	<p>
	<a href="polls.php">New Poll</a>
					<br /> 
		<a href="/poll.php">Main Poll Page</a> 
		<?php
if ($poll_question_id) {
?>
					<br /> 
					<a href="/poll.php?poll_question_id=<?php echo $poll_question_id
?>">View Public Display of Poll Number <?php echo $poll_question_id ?></a>
				<?php
}
?>
	</p>

 <form action="" method="GET">
 <p class="input_type">
Poll question:
<br />
<textarea rows="10" cols="50" name="html"><?php echo $html
?>
</textarea> <br />
</p>
<p class="input_type">
Type: <br />
<?php
if ($type == 'single') {
	$single = 'checked';
} else {
	$multiple = 'checked';
}
?>
Single select: <input type="radio" name="type" value="single" <?php echo $single ?> >
Multiple select: <input type="radio" name="type" value="multiple" <?php echo $multiple ?> ><br />
</p>
<input type="hidden" name="poll_question_id" value="<?php echo $poll_question_id ?>">
<input type="hidden" name="submitted" value="1">

<p class="input_type">
Possible Answers: 
<br />
<span style="font-size:.7em"><a href="#" onClick="more_answers();">More Answers</a></span>
<span style="font-size:.7em">The responses will always appear alphabetically, so just number them to force an order (or sneak spaces in front of the HTML).</span>
<span id="answers">
<?php
if (!$first_view) {
	$response_tick = 1;
	foreach(array_keys($responses) as $old_poll_choice_id) {
		echo "<div>Response:  " . '<textarea rows="2" cols="40" name="old-answer-' . $old_poll_choice_id . '">' . $responses[$old_poll_choice_id] . '</textarea>' . "\n" . 'Delete: <input type="checkbox" name="delete-' . $old_poll_choice_id . '"></div>';
		echo "\n";
		$response_tick++;
	}
}
?>
</span>
</p>

<input type="submit" value="Submit">
</form>
<h3> Existing Polls </h3>
<?php
list_polls() ?>

<?php
if ($first_view) {
?>

	<script>

	for(i=0;i<3;i++) {
		more_answers();
}
	</script>

	<?php
}
?>
</body>
</html>


<?php
db_disconnect($dbh);
function fetch_responses()
{
	$new_responses = array();
	$deleted_responses = array();
	$updated_responses = array();
	foreach(array_keys($_GET) as $key) {
		if (preg_match('/^new-answer-(\d+)/', $key, $matches)) {
			if ($_GET[$key]) {
				$new_responses[] = $_GET[$key];
			}
		}
		if (preg_match('/old-answer-(\d+)/', $key, $matches)) {
			$number = $matches[1];
			if (!$_GET["delete-$number"] && $_GET[$key]) {
				$updated_responses[$number] = $_GET[$key];
			}
		}
		if (preg_match('/delete-(\d+)/', $key, $matches)) {
			$number = $matches[1];
			$deleted_responses[] = $number;
		}
	}
	return array('new' => $new_responses, 'deleted' => $deleted_responses, 'updated' => $updated_responses);
}
function list_polls()
{
	$sql = "select html, poll_question_id from poll_questions order by poll_question_id desc";
	$res = mysql_query($sql);
	echo '<table>';
	while ($new_row = mysql_fetch_array($res)) {
		echo '<tr><td>';
		echo $new_row['html'];
		echo '</td><td>';
		echo ' <a href="?poll_question_id=';
		echo $new_row['poll_question_id'];
		echo '">Edit</a>';
		echo '</td></tr>';
		echo "\n";
	}
	echo '</table>';
}
?>

