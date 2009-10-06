<?php

require_once('bootstrap.php');

$migration = new Doctrine_Migration('db/migrations');

// If we got a command line argument, use that as the migration target
if ($argc == 2) {
	$migrate_to = $argv[1];
	$migration->migrate($migrate_to);
} else {
	$migration->migrate();
}

