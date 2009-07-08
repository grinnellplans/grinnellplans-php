<?php
require_once('Plans.php');
require_once('functions-main.php');

class ClickstreamEvent {
	private static $instances = 0;
	
	function ClickstreamEvent() {
		if (ClickstreamEvent::$instances == 0) {
			$dbh = db_connect();
			$userid = User::id();
			$userid = addslashes($userid);
			if (!$userid) {
				$userid = 0;
			}
			$ip = addslashes($_SERVER['REMOTE_ADDR']);
			$script_uri = addslashes($_SERVER['PHP_SELF']);
			$query_string = addslashes($_SERVER['QUERY_STRING']);
			$sql = "insert into clickstream set userid = $userid,
			ip = '$ip', 
			script_uri = '$script_uri', 
			query_string = '$query_string',	created = now() ";
			mysql_query($sql);
		}
	}
}
?>