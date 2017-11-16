<?php
/**
 * rpc-protobuf 调试
 * User: Terry
 * Date: 2017/11/5
 * Time: 22:42
 */

$autoloader = require '../vendor/autoload.php';
$autoloader->addPsr4('', ['../src']);

define('TRANSPORT_TYPE', 'NEW');

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

try {
    $ip = '192.168.10.10';
    $port = 43217;
    $soaRpc = new \Rpc\Client\SoaRpcClient($ip, $port,5);
    $soaRpc->initRequestHeader([
        "type"=> 1,
        "version"=> "1.0.0",
        "domain"=> "",
        "tokenId"=> "a7f1db0a670e3c3cabf81b62975f5891"
    ]);

    $body = [
        "type" => 1,
        "platform" => "1",
        "pageSize" => 20,
        "pageNo" => 1,
        "siteCode" => "GLB"
    ];

    $method = 'queryLoginfo';
    $soaServer = "com.globalegrow.spi.mpay.inter.PaySystemService";
    $return = $soaRpc->call('queryLoginfo', $body, $soaServer);

    var_dump($return);

}catch (Exception $e) {
    var_dump($e);
    
    if ($e instanceof \Rpc\Exceptions\RpcException) {
        var_dump($e->getMessage());
    }else{
        var_dump($e->getMessage());
    }

//    var_dump($e, $e->getMessage());
}



