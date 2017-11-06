<?php
/**
 * rpc-protobuf 调试
 * User: Terry
 * Date: 2017/11/5
 * Time: 22:42
 */

$autoloader = require '../vendor/autoload.php';
$autoloader->addPsr4('', ['../php_out', '../libs']);

// test socket
$demoScript = new \MySockets\RequestBySocket();
$demoScript->run();

