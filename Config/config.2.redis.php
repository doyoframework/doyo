<?php
$GLOBALS['REDIS']['session'] = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '',
    'timeout' => 30,
    'pconnect' => false,
    'database' => 0
);

$GLOBALS['REDIS']['crontab'] = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '',
    'timeout' => 30,
    'pconnect' => false,
    'database' => 1
);

$GLOBALS['REDIS']['online'] = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '',
    'timeout' => 30,
    'pconnect' => false,
    'database' => 2
);

$GLOBALS['REDIS']['cache'] = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '',
    'timeout' => 30,
    'pconnect' => false,
    'database' => 3
);

$GLOBALS['REDIS']['lock'] = array(
    ['127.0.0.1', 6379, 0.01]
);
