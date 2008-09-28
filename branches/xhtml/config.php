<?php
/* This file should contain all the modifiable settings for Plans.  For
* better security, consider moving this file to an area not reachable by the
* webserver, and changing dbconfig.php to simply point to the new location.
* For example, require("../dbconfig.php").
*/
require_once('Configuration.php');
// Database login and password
$dblogin = MYSQL_USER;
$dbpasswd = MYSQL_PASS;
// The table within the database dedicated to plans stuff
$dbtable = MYSQL_DB;
// Which server the database is on
$dbserver = MYSQL_HOST;
//Define your Timezone - the default is to US/Central Time
$TZ = TZ;
putenv("TZ=$TZ");
//What version of Plans is this?
define("PLANSVERSION", "2.4");
define("PLANSVNAME", "Plans " . PLANSVERSION);
//Load the legal disclaimer file (Your needs may differ!)
require ("legal.php");
?>
