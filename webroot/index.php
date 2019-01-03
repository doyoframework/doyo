<?php
define('APP_PATH', dirname(__DIR__));

require_once(APP_PATH . "/doyo/framework.php");

use Core\Context;

$dispatcher = Context::HttpDispatcher();

$dispatcher->dispatch();