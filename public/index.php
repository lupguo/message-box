<?php
/**
 * rpc-protobuf 调试
 * User: Terry
 * Date: 2017/11/5
 * Time: 22:42
 */

$autoloader = require '../vendor/autoload.php';
$autoloader->addPsr4('', ['.']);

// test socket
//$demoScript = new \MySockets\RequestBySocket();
//$demoScript->run();

//rpc客户端

$body = [
    "type" => 1,
    "platform" => 1,
    "pageSize"	=> 20,
    "pageNo"	=> 1,
    "siteCode"	=> "GLB",
];

$streamClient = new \Rpc\Transport\Streams\TcpStream();

