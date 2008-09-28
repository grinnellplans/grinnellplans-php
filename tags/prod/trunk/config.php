<?php
/*
	GrinnellPlans - configuration file
	Version Nov-11-05-1 (Laiu-Draft-1)
	Notes: You may adjust all the required settings here to run Plans
*/

require_once('Configuration.php');

//Define your Timezone - the default is to US/Central Time
$TZ=TZ;
putenv ("TZ=$TZ");

//Load the legal disclaimer file (Your needs may differ!)
require("legal.php");

?>
