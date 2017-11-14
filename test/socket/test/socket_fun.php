<?php
/**
 * 套接字相关函数调试
 *
 * @Author  Terry
 * @Date    2017/11/13 11:49
 */
$listenIp = '0.0.0.0';
$listenPort = 43215;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

//允许监听同样的地址
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

socket_bind($socket, $listenIp, $listenPort);

socket_listen($socket, 10);

printf("server listen %s:%d \n", $listenIp, $listenPort );

while (true) {

    $clientSocket = socket_accept($socket);

    var_dump($socket, $clientSocket);

//    $serverStatus = socket_get_status($socket);
//    $clientStatus = socket_get_status($clientSocket);

//    printf("server status: \n%s", $serverStatus);
//    printf("client status: \n%s", $clientStatus);

    printf("getprotobyname()\n");

    var_dump(getprotobyname('tcp'), getprotobyname('udp'));

    echo "getservbyname()\n";
    var_dump(getservbyname('www','tcp'), getservbyname('https','tcp'), getservbyname('ssh', 'tcp') );

    printf("getservbyport() \n");
    var_dump(getservbyport(80, 'tcp'), getservbyport('443', 'tcp'));

    echo "socket_get_option():";
    var_dump (socket_get_option($socket ,SOL_SOCKET ,SO_REUSEADDR ));

    echo "socket_getpeername():";
    var_dump(socket_getpeername($clientSocket, $clientAddress, $clientPort));
    printf("client address: %s, client port: %s \n", $clientAddress, $clientPort);

    echo "socket_getsockname():";
    var_dump(socket_getsockname($socket, $serverAddress, $serverPort));
    printf("server address: %s, server port: %s \n", $serverAddress, $serverPort);

    //获取客户端请求数据
    while (true) {
        $clientInput = trim(socket_read($clientSocket, 1024));

        //客户端输入
        if (!empty($clientInput)) {
            printf("Client Input: \n-----\n%s\n--------\n", $clientInput);
        }

        //客户端请求HTTP
        if ($clientInput == 'http') {
            //服务端告知客户端准备OK了，可以传递相关的HTTP请求数据过来了
            $successWriteLen = socket_write($clientSocket, '{"status": "ok"}', 1024);
            if ($successWriteLen === false ) {
                printf("HTTP SOCKET WRITE FALSE !!\n");
                break;
            }else {
                printf("HTTP SOCKET WRITE SUCCESS !!\n");
            }

            socket_shutdown($clientSocket);
            socket_close($clientSocket);
            break;
        }

        //退出客户端联机
        if ($clientInput == 'quit') {
            printf("CLIENT QUIT !! \n");

            socket_write($clientSocket, json_encode([
                'status' => 'ok',
                'time'  => date('Y/m/d H:i:s'),
                'msg'   => "BYE BYE !!",
            ]));

            socket_shutdown($clientSocket);
            socket_close($clientSocket);
            break;
        }
    }

    sleep(1);
}


