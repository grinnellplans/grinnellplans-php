<?php
require_once("Plans.php");

class ProjectInformation {

	public static function projectUrl() {
		return "http://code.google.com/p/grinnellplans/";
	}

	public static function revision() {
		if (file_exists(__ROOT__ . '/.svn/entries')) {
			$svn = file(__ROOT__ . '/.svn/entries');
			if (is_numeric(trim($svn[3]))) {
				$version = $svn[3];
			} else {
				// pre 1.4 svn used xml for this file
				$version = explode('"', $svn[4]);
				$version = $version[1];    
			}
			return trim($version);
		}
		return 0;
	}
	
	public static function version() {
		return "r" . ProjectInformation::revision();
	}
	
	public static function bugReportUrl() {
		$username = User::name();
		$revision = ProjectInformation::revision();
		$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];

		$comment = "Username: [$username]\n" .
				"Revision: $revision\n" .
				"URL: $url\n" .
				"User-agent: $useragent\n\n" . 
				"How can we reproduce the problem?\n1. \n2. \n3. \n\nWhat is the expected output? What do you see instead?\n ";

		$parameters = "?comment=" . urlencode($comment);
		return ProjectInformation::projectUrl() . "issues/entry" . $parameters;
	}	
}
?>
