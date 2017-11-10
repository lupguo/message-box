<?php
$clients = array();
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
socket_bind($socket,'127.0.0.1', 43211);
socket_listen($socket);
socket_set_nonblock($socket);

while(true)
{
    if(($newc = socket_accept($socket)) !== false)
    {
        var_dump($newc);
        echo "Client $newc has connected\n";
        $clients[] = $newc;
    }
}

var_dump($clients);
