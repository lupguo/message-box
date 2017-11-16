<?php
/**
 * SOA相关接口测试
 *
 * @author  Terry (psr100)
 * @date    2017/11/16
 * @since   2017/11/16 9=>21
 */



$autoloader = require '../vendor/autoload.php';
$autoloader->addPsr4('', ['../src']);

define('TRANSPORT_TYPE', 'OBS');

// test socket
//$demoScript = new \MySockets\RequestBySocket();
//$demoScript->run();

//rpc模拟
$header = [
    'service' => 'com.globalegrow.spi.morder.common.inter.OrderQueryService',
    'method' => 'orderInfoList',
    'domain' => '',
    'version' => '1.0.0',
    'type' => 1,
    'mId' => '',
    'url' => '',
    'tokenId' => 'a7f1db0a670e3c3cabf81b62975f5891',
];

$body = [
    'createStartTime' => '1508204763',
    'createEndtime' => '1510796763',
    'pageSize' => '10',
    'pageNo' => '1',
    'siteCode' => 'GB',
];

try {
    $ip = '10.40.2.106';
    $port = 2087;
    $soaRpc = new \Rpc\Client\SoaRpcClient($ip, $port,5);
    $soaRpc->initRequestHeader($header);

    //订单SOA接口调试
    $method = 'orderInfoList';
    $soaServer = "com.globalegrow.spi.morder.common.inter.OrderQueryService";
    $return = $soaRpc->call($method, $body, $soaServer);

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