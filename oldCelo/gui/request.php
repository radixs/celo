<?php
require_once '../core/initialise.php';

err_log('****************** REQUEST ********************'); //TODO remove me

$decoded_json = json_decode(file_get_contents("php://input"), true);

$connector = new Library\Utilities\Connector($db_config, 'Gateway');
$db        = $connector->connect();
$request   = new Library\Controllers\Request($db);
$request->process_request($decoded_json);
