<?php
require_once ('Plans.php');
require ("functions-main.php"); //load main functions
require ("syntax-classes.php"); //load display functions
$idcookie = User::id();
$userid = $idcookie;
$dbh = db_connect(); //connect to database
$thispage = new PlansPage('Utilities', 'poll', PLANSVNAME . ' - Polls', 'poll.php');
if (User::logged_in()) {
    populate_page($thispage, $dbh, $idcookie);
} else {
    populate_guest_page($thispage);
    $denied = new AlertText('Please log in.', 'Access Denied');
    $thispage->append($denied);
}
if (User::logged_in()) {
    $poll_question_id = $_GET['poll_question_id'];
    $submitted = $_GET['submitted'];
    if (!$poll_question_id) {
        $sql = "select max(poll_question_id) as max from poll_questions; ";
        $res = mysqli_query($dbh,$sql);
        $new_row = mysqli_fetch_array($res);
        $poll_question_id = $new_row['max'];
    }
    $responses = array();
    $sql = "select type, q.html as question from poll_questions q 
	where poll_question_id = $poll_question_id";
    $res = mysqli_query($dbh,$sql);
    $new_row = mysqli_fetch_array($res);
    $question = $new_row['question'];
    $type = $new_row['type'];
    if ($submitted) {
        $poll_choice_ids = array();
        $poll_choice_id = $_GET['poll_choice_id'];
        if (!isset($poll_choice_id)) {
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
        mysqli_query($dbh,$sql);
        foreach($poll_choice_ids as $poll_choice_id) {
            $sql = "insert into poll_votes set userid = $userid,
			created = now(),
			poll_choice_id = $poll_choice_id";
            mysqli_query($dbh,$sql);
        }
    }
    $heading = new HeadingText($question, 3);
    $thispage->append($heading);
    $votingform = new Form('pollform', true);
    $votingform->method = 'GET';
    $thispage->append($votingform);
    $sql = "select c.html as html, c.poll_choice_id as poll_choice_id, v.userid as checked from poll_choices c left join poll_votes v on v.userid = $userid and v.poll_choice_id = c.poll_choice_id where c.poll_question_id = $poll_question_id order by c.html";
    $html_res = mysqli_query($dbh,$sql);
    while ($new_row = mysqli_fetch_array($html_res)) {
        $html = $new_row['html'];
        $checked = $new_row['checked'];
        $poll_choice_id = $new_row['poll_choice_id'];
        $html = $new_row['html'];
        $set = new FormItemSet('polloption', false);
        if ($type == 'single') {
            $item = new RadioInput('poll_choice_id', $poll_choice_id);
        } else {
            $item = new CheckboxInput('poll_choice_id[]', $poll_choice_id);
        }
        $item->title = $html;
        $item->checked = $checked;
        $set->append($item);
        $sql = "select count(*) as popularity from poll_votes v
		where v.poll_choice_id = $poll_choice_id";
        $res = mysqli_query($dbh,$sql);
        $new_row = mysqli_fetch_array($res);
        $popularity = $new_row['popularity'];
        $votes = new RegularText($popularity, 'Popularity');
        $set->append($votes);
        $votingform->append($set);
    }
    $item = new HiddenInput('poll_question_id', $poll_question_id);
    $votingform->append($item);
    $item = new HiddenInput('submitted', 1);
    $votingform->append($item);
    $item = new SubmitInput('Vote!');
    $votingform->append($item);
    $sql = "select count(*) as voted from poll_votes v join poll_choices c using (poll_choice_id) where userid = $userid and poll_question_id = $poll_question_id";
    $res = mysqli_query($dbh,$sql);
    $new_row = mysqli_fetch_array($res);
    $voted = $new_row['voted'];
    if ($voted) {
        $msg = new InfoText('You have voted in this poll, but you may change your mind.', 'Vote registered');
        $thispage->append($msg);
    }
}
$thispage->append(list_polls());
$ask = new RequestText('Poll ideas?  <a href="mailto:grinnellplans@gmail.com">Email</a>.', 'Feedback?');
$thispage->append($ask);
function list_polls() {
    $list = new WidgetList('polls_list', true, 'All Polls');
    $sql = "select html, poll_question_id from poll_questions where poll_question_id not in (16, 17) order by poll_question_id desc";
    $res = mysqli_query($dbh,$sql);
    while ($new_row = mysqli_fetch_array($res)) {
        $link = new Hyperlink('poll_link', false, '?poll_question_id=' . $new_row['poll_question_id'], preg_replace('/<[^>]*>/', '', $new_row['html']));
        $list->append($link);
    }
    return $list;
}
interface_disp_page($thispage);
db_disconnect($dbh);
?>
