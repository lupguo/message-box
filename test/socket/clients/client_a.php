<?php
/**
 * 套接字客户端调试
 *
 * @Author  Terry
 * @Date    2017/11/13 14:39
 */

$clientSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_set_option($clientSocket, SOL_SOCKET, SO_REUSEADDR, 1);

//连接套接字
$connectIp = '192.168.10.10';
$connectPort = 43215;
socket_connect($clientSocket, $connectIp, $connectPort) or die('Connect failed');

//客户端命令行参数
$uReq = request([
    'action:',
]);

switch ($uReq['action']) {

    case 'http' : {
        $socketContent = getHttpRequest();
        break;
    }

    case 'quit' : {
        $socketContent = getQuitRequest();
        break;
    }

    default : {
        die("ERROR ACTION, USAGE {--action=http|quit} !! \n");
    }
}

//请求套接字
requestContent($clientSocket, $socketContent);

//接收请求
$returnData = receiveContent($clientSocket);

//服务端数据检测
if (!empty($returnData)) {
    printf("[ %s ] SERVER RETURN DATA TOTAL STRING : %s \n",  date("H:i:s"), $returnData);
    $jsonReturn = json_decode($returnData, true);
    if ($jsonReturn != false) {
        var_dump($jsonReturn);

        //发送额外的http请求
        if ($uReq['action'] == 'http')  {
            printf("CLIENT SEND HTTP REQUEST !!\n");

            //请求套接字
            $host   = isset($uReq['host']) ? $uReq['host'] : 'localhost';
            $method = isset($uReq['method']) ? $uReq['method'] : 'localhost';
            $path   = isset($uReq['path']) ? $uReq['path'] : '/';
            requestContent($clientSocket, getHttpContent($host, $method, $path));

            //接收请求
            $returnData = receiveContent($clientSocket);
            printf("SERVER RETURN %s \n", $returnData);
        }

        //结束请求
        finishedRequest($clientSocket);
    }
}

//结束请求
function finishedRequest($socket) {
//    socket_shutdown($socket);
    socket_close($socket);
}

//发送客户端请求
function requestContent($socket, $content) {
    socket_write($socket, $content) or die("socket write failed !!");
}

//接收套接字内容
function receiveContent($socket, $packageSize = 15) {
    $count  = 0;
    $data   = '';

//    socket_set_nonblock($socket);

    while (true) {
        //接收服务端响应
//        $buffLen = socket_recv($socket, $buff, 10, MSG_DONTWAIT);
        $buff = socket_read($socket, $packageSize);
        $data .= $buff;

        printf("[%d][ %s ] SERVER RETURN BUFF : %s \n", ++$count , date("H:i:s"), $buff);

        if (empty($buff)) {
            printf("SOCKET READ FINISHED !!\n");
            break;
        }
    }

    return $data;
}


//http content
function getHttpContent($host = 'localhost', $method = 'GET', $path = '/', $version = 'HTTP/1.1'){
    //发送请求
    $httpRequestArr = [
        sprintf("%s %s %s", $method, $path, $version),
        sprintf("Host: %s", $host),
        sprintf("Cache-Control: no-cache"),
        sprintf("User-Agent: Terry-Socket-UA"),
        sprintf("Accept-Language: zh-CN"),
        sprintf("Connection: Keep-Alive"),
        sprintf("Accept: */*"),
    ];

    return join("\n", $httpRequestArr);
}

//quit client
function getQuitRequest() {
    return 'quit';
}

//http request client
function getHttpRequest() {
    return 'http';
}

//获取命令行参数
function request(array $options = []) {
    // Set the default values.
    $defaults = [
        'action' => '',
        'username' => posix_getpwuid(posix_geteuid())['name'],
        'env' => ''
    ];
    $options += $defaults;

    // Sufficient enough check for CLI.
    if ('cli' === PHP_SAPI) {
        return getopt('', ['action:', 'os::', 'username::', 'env::']) + $options;
    }
    return $_GET + $options;
}
