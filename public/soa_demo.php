<?php
/**
 * SOA相关接口测试
 *
 * @author  Terry (psr100)
 * @date    2017/11/16
 * @since   2017/11/16 9=>21
 */


$rootPath   = dirname(__DIR__);
$autoloader = require $rootPath.'/vendor/autoload.php';
$autoloader->addPsr4('', [$rootPath.'/src']);

// test socket
//$demoScript = new \MySockets\RequestBySocket();
//$demoScript->run();

//rpc模拟
$header = [
    'service' => 'com.globalegrow.spi.morder.common.inter.OrderQueryService',
    'method'  => 'orderInfoList',
    'domain'  => '',
    'version' => '1.0.0',
    'type'    => 1,
    'mId'     => '',
    'url'     => '',
    'tokenId' => 'a7f1db0a670e3c3cabf81b62975f5891',
];

$body = [
    'createStartTime' => '1508949941',
    'createEndtime'   => '1511541941',
    'pageSize'        => '10',
    'pageNo'          => '1',
    'siteCode'        => 'GB',
];

try {
    $ip   = '10.40.2.106';
    $port = 2087;

    $transport  = new \Rpc\Transport\Stream\SoaStreamTransport($ip, $port, 3);
    $messageBox = new \Rpc\Message\SoaMessageBox($header);
    $rpcClient  = new \Rpc\Client\RpcClient($transport, $messageBox);

    //订单SOA接口调试
    $method = 'orderInfoList';
    $server = "com.globalegrow.spi.morder.common.inter.OrderQueryService";
    $return = $rpcClient->call($method, $body, $server);

    echo json_encode($return);

} catch (Exception $e) {
    if ($e instanceof \Rpc\Exceptions\RpcException) {
        var_dump('RPC Exception: '.$e->getMessage());
    } else {
        var_dump($e->getMessage());
    }
}