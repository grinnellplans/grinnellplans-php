<?php
require_once ('../Plans.php');
Doctrine::generateModelsFromYaml('../db/schema.yaml', '../db/');
?>
