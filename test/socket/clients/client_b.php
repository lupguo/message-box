<?php
/**
 * fsockopen()
 *
 * @Author  Terry
 * @Date    2017/11/14 0:08
 */

//客户端打开套接字
$fp = fsockopen('localhost', 43212, $errno, $errstr, 30) or die(sprintf("FSOCKEOPEN FAILED [%d]: %s", $errno, $errstr));

//请求数据
printf("use fgets test fscokeopen..!! \n");

//ini_set('auto_detect_line_endings',1);

//use while on feof
//printf( "use while feof\n");
//$body = '';

//while (! feof($fp)) {
//    $body .= fgets($fp, 1024);
//
//    var_dump($body, feof($fp));
//
//    sleep(1);
//}
//
//var_dump($body);

//use while true
printf(  "use while true");
do {
    $rs = fgets($fp, 1024);

    var_dump($rs, trim($rs));

    if ($rs === false)
        break;

    sleep(1);
} while(true);

printf("Game Over !!");
fclose($fp);




