<?php
/**
 * BLOCK SOCKET DEMO ...
 *
 * @Author  Terry
 * @Date    2017/11/12 18:27
 */

//socket create
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('create failed !!');

//socket set options
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1) or die('socket set options failed !!');

//socket set block will block receive/send/connect/accept action.
socket_set_block($socket);

//socket bind
$listenIp = '0.0.0.0';
$listenPort = 43212;
socket_bind($socket, $listenIp, $listenPort) or die('socket bind failed !!');

//socket listen
socket_listen($socket, 10) or die('socket listen failed !!');

//client accept
printf("[%s] Start Block Socket Server ON %s:%d ... \n", date("Y-m-d H:i:s"), $listenIp, $listenPort);

while (true) {
    printf("\nWaiting for new client connect in \n>>>>>>>>\n");
    
    $clientSocket = socket_accept($socket);
    
    printf("Client[%s] connected \n", $clientSocket);

    socket_write($clientSocket, sprintf("WELCOME, %s\n", $clientSocket), 1024);

    sleep(2);
    socket_write($clientSocket, "Hi, man !!\n", 1024);

    sleep(2);
    socket_write($clientSocket, "Welcome, have fun !!\n", 1024);

    //针对客户端连接读/写
    while (true) {
        $rawInput = socket_read($clientSocket, 1024) or die(sprintf("client socket_read error: %s !!\n", socket_strerror(socket_last_error($clientSocket))));
        $input = empty($rawInput) ? '' :trim($rawInput);
        
        printf("client input data: %s \n", $input);
        
        //quit
        if ( $input == 'quit' ) {
            socket_write($clientSocket, sprintf("Quit current client connect , %s \n", $clientSocket));
    
            //close client connect socket
            socket_shutdown($clientSocket);
            socket_close($clientSocket);
            
            printf("Client[%s] quit \n<<<<<<<<<<\n", $clientSocket);
            break 1;
        }
    
        //shutdown
        if( $input == 'shutdown' ) {
            socket_write($clientSocket, sprintf("Quit current client connect and exit server socket !!", $clientSocket));
            
            //close client connect socket
            socket_shutdown($clientSocket);
            socket_close($clientSocket);
    
            //close server socket
            socket_shutdown($socket);
            socket_close($socket);
            break 2;
        }
        
    }
    
}


