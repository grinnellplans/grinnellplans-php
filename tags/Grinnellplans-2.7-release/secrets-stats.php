<?php
require_once('Plans.php');
new SessionBroker();

require('functions-main.php');
$dbh = db_connect();
$idcookie = User::id();
if (User::logged_in()) {
	mdisp_begin($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
	$sql = 'select date, yes, no, 
    substr(concat("  ",100*no/(yes+no),"%"), -7) as percent_no 
    from (select date(date) as date, sum(if(display="yes",1,0)) as yes,
    sum(if(display="no",1,0)) as no  from secrets where date > "2005-12-04"
    group by date) as m';
	$cmd = "mysql -u " . MYSQL_USER . " --password='" . MYSQL_PASSWORD . "' " . MYSQL_DB . " -H -e '$sql'";
	system($cmd);
} else
//begin guest user display
{
	gdisp_begin($dbh);
}
if (User::logged_in()) //if is a valid user, give them the option of putting the plan on their autoread list, or taking it off, and also if plan is on their autoread list, mark as read and mark time
{
	mdisp_end($dbh, $idcookie, $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], get_myprivl());
} else {
	gdisp_end();
}
db_disconnect($dbh);
?>
