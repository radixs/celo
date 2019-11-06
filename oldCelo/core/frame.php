<?php
require_once 'initialise.php';

$connector        = new Library\Utilities\Connector($db_config, 'Gateway');
$db               = $connector->connect();
$frame_controller = new Library\Controllers\FrameController($config, $db);
$frame_controller->run($db);
