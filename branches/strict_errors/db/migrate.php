<?php

require_once('bootstrap.php');

$migration = new Doctrine_Migration('db/migrations');
$migration->migrate();

