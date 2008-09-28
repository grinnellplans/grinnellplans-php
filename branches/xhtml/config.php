<?php
/* This file should contain all the modifiable settings for Plans.  For
* better security, consider moving this file to an area not reachable by the
* webserver, and changing dbconfig.php to simply point to the new location.
* For example, require("../dbconfig.php").
*/
// Database login and password
$dblogin = "plans";
$dbpasswd = 'foobar';
// The table within the database dedicated to plans stuff
$dbtable = 'planstest';
// Which server the database is on
$dbserver = 'localhost';
//Define your Timezone - the default is to US/Central Time
$TZ = 'America/Chicago';
putenv("TZ=$TZ");
//What version of Plans is this?
define("PLANSVERSION", "2.4");
define("PLANSVNAME", "Plans " . PLANSVERSION);
//Load the legal disclaimer file (Your needs may differ!)
require ("legal.php");
?>
