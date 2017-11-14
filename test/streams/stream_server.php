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
$stream = stream_socket_server(sprintf("tcp://%s:%s",$listenIp,$listenPort), $errno, $errstr, STREAM_SERVER_LISTEN);

echo sprintf("tcp://%s:%s",$listenIp,$listenPort)."\n";

if (!$stream) {
    echo "$errstr($errno)\n";
} else {
    //超时配置
    var_dump(ini_get('default_socket_timeout'));

    while ($conn = stream_socket_accept($stream)) {
        //流相关的信息
        var_dump(stream_socket_get_name($conn, true), stream_socket_get_name($conn,false));

        fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
        fclose($conn);
    }
    fclose($stream);
}