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

$header = [
    "type"=> 1,
    "version"=> "1.0.0",
    "domain"=> "",
    "tokenId"=> "a7f1db0a670e3c3cabf81b62975f5891"
];

try {
    $ip = '192.168.10.10';
    $port = 43217;
    
    $transport = new \Rpc\Transport\Stream\StreamTransport($ip, $port, 3);
    $messageBox = new \Rpc\Message\SoaMessageBox($header);
    $rpcClient = new \Rpc\Client\RpcClient($transport, $messageBox);

    $method = 'orderInfoList';
    $soaServer = "com.globalegrow.spi.morder.common.inter.OrderQueryService";
    
    $return = $rpcClient->call($method, $body, $soaServer);
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



