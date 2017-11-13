<?php
/**
 * NO BLOCK SOCKET SERVER DEMO ...
 *
 * @Author  Terry
 * @Date    2017/11/12 20:51
 */

//socket create
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('socket create failed !!');

//socket set options
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1) or die('socket set option failed !!');
socket_set_nonblock($socket) or die('socket set no block failed !!');

//socket bind
socket_bind($socket, '0.0.0.0', 43213) or die('socket bind failed !!');

//socket listen
socket_listen($socket, 10) or die('socket listen failed !!');

//socket accept
printf("[%s] Start Non Block Socket Server ... \n", date("Y-m-d H:i:s"));

$clientSockets = [];
$no = 0;

while (true) {

    // Make list of sockets to listen for changes in, including host
    $readSocket = [$socket];

    // get a list of all the clients that have data to be read from
    $ready = socket_select($readSocket, $write = null, $except = null, 0);

    if ($ready === false) {
        die("Failed to listen for clients: " . socket_strerror(socket_last_error()));
    } elseif ($ready > 0) {

        if ($currentSocket = socket_accept($socket) !== false) {
            $clientSockets[++$no] = $currentSocket;

            //server prompt
            printf("Client connect, total clients is %d", count($clientSockets));

            //socket write to client
            socket_write($currentSocket, sprintf("Have one client connect. Welcome [%d]", $no));

            while (true) {

                $rawInput = socket_read($clientSocket, 1024);
                $input = empty($rawInput) ? '' : trim($rawInput);

                if (!empty($input)) {
                    printf("client input data: %s \n", $input);
                }

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

            };
        }

    }
    //sleep(1);
}

