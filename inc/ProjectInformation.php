<?php
require_once ("Plans.php");
class ProjectInformation {
    public static function recentBugsUrl() {
        return "https://github.com/grinnellplans/grinnellplans-php/issues";
    }
    public static function projectUrl() {
        return "https://github.com/grinnellplans/grinnellplans-php/";
    }
    public static function revision() {
        if (file_exists(__ROOT__ . '/.git/refs/heads/master')) {
            $git = file(__ROOT__ . '/.git/refs/heads/master');
	    $version = substr($git[0],0,8);
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
        $comment = "Username: [$username]\n" . "Revision: $revision\n" . "URL: $url\n" . "User-agent: $useragent\n\n" . "How can we reproduce the problem?\n1. \n2. \n3. \n\nWhat is the expected output? What do you see instead?\n ";
        $parameters = "?" . http_build_query(array('body' => $comment));
        return ProjectInformation::projectUrl() . "issues/new" . $parameters;
    }
}
?>
