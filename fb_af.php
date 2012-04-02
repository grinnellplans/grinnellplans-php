<?php
$diff = $_SESSION['d'];
if ($diff == "") {
	$diff = "A quick plan update";
}
$diff = str_replace("\n", " ", $diff);
?>
<div id="fb-root"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src='https://connect.facebook.net/en_US/all.js'></script>
<script>
$(function() { 

FB.init({appId: "278870582191344", status: true, cookie: true});

$(".infomessage").append("<br/><br/><a id='fbin' href='#'><img height='32' valign='middle' src='/f_logo.png' /> Let Facebook know!</a><br/>");
$("#fbin").click(function(e) {
	var obj = {
		method: 'feed',
		link: 'http://www.grinnellplans.com/read.php?searchname=<?php echo $searchname?>',
		name: '[<?php echo $searchname?>]\'s Updated Plan',
		description: 'Wrote \'<?php echo $diff?>\'',
		caption: 'Plans is a vibrant community of Grinnellians',
	};
	FB.ui(obj, function() {});
});

});
</script>
