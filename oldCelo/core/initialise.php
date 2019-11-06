<?php
require_once 'basic_helpers.php';
require_once 'vendor/autoload.php';
date_default_timezone_set('Europe/London');

$config    = new Library\Utilities\Config();
$db_config = $config->core_global_path.$config->database_ini_location;
