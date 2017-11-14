<?php
/**
 * 流相关的函数
 *
 * @Author  Terry
 * @Date    2017/11/13 23:31
 */

//stream_wrapper_register()

//stream_get_wrappers()

//流包裹、传输方式、过滤器
//var_dump(stream_get_wrappers(), stream_get_transports(), stream_get_filters());

//上下文
//stream_context_create();    //fopen, file(),file_get_contents()

//上下文参数选项:与特定协议相关
//stream_context_set_option();

//上下文参数变量：所有协议都可以设置
//stream_context_set_params();

//建立在套接字基础上的读写操作设置操作时间设置连接时限
//stream_set_timeout();

//创建客户端流相关连接
$hostIp = '192.168.10.10';
$hostPort = 43217;
$connectTimeOut = 3;
$stream = stream_socket_client(sprintf("%s:%d", $hostIp, $hostPort), $errno, $errstr, $connectTimeOut);

//设置流的读/写超时
stream_set_timeout($stream, 5);
$res = fread($stream, 1024);

//流相关数据信息, stream_get_meta_data ( $fp );
$info = stream_get_meta_data($stream);
echo $info['timed_out'] ? "stream read timeout !! \n" : "read final !!\n";
var_dump($info);

