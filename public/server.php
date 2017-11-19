<?php
/**
 * soa服务端模拟
 *
 * @Author  Terry
 * @Date    2017/11/16 0:27
 */

$rootPath   = dirname(__DIR__);
$autoloader = require $rootPath.'/vendor/autoload.php';
$autoloader->addPsr4('', [$rootPath.'/src']);

$responseBody = [
    'data'   => [
        'id'       => 999,
        'username' => 'terry',
        'email'    => 'tkstorm@163.com',
    ],
    'status' => 200,
];
$status       = 200;
$message      = 'hi, man';
(new \Rpc\Server\StreamServerDemon())->start($status, $message, $responseBody);