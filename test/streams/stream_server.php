<?php
/**
 * 流相关的服务
 *
 * @Author  Terry
 * @Date    2017/11/14 13:06
 */

//基于流，创建一个相关的服务
$listenIp = '0.0.0.0';
$listenPort = 43217;
$local_socket = sprintf("tcp://%s:%d",$listenIp,$listenPort);
$stream = stream_socket_server($local_socket, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);

var_dump($local_socket, $stream, stream_get_meta_data($stream));

if (!$stream) {
    echo "$errstr($errno)\n";
} else {
    //超时配置
    while ($conn = stream_socket_accept($stream, -1, $clientPeer)) {
        //流相关的信息
        var_dump(stream_socket_get_name($conn,true), stream_socket_get_name($conn,false));

        fwrite($conn, 'The local time is ' . date('n/j/Y g:i a'));
        fclose($conn);
    }
    fclose($stream);
}