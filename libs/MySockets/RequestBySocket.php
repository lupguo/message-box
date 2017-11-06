<?php
/**
 * Request By Socket
 * User: Terry
 * Date: 2017/11/6
 * Time: 22:36
 */

namespace MySockets;


class RequestBySocket
{
    public function run() {

        $sock = fsockopen("ssl://www.baidu.com", 443, $errno, $errstr, 120);
        if (!$sock) die("$errstr ($errno)\n");

        $data = "foo=" . urlencode("Value for Foo") . "&bar=" . urlencode("Value for Bar");

        fwrite($sock, "GET / HTTP/1.1\r\n");
        fwrite($sock, "Host: www.baidu.com\r\n");
        fwrite($sock, "Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($sock, "Content-length: " . strlen($data) . "\r\n");
        fwrite($sock, "Accept: */*\r\n");
        fwrite($sock, "\r\n");
        fwrite($sock, $data);

        $headers = "";
        while ($str = trim(fgets($sock, 4096)))
            $headers .= "$str\n";

        echo "\n";

        $body = "";
        while (!feof($sock))
            $body .= fgets($sock, 4096);

       
        fclose($sock);

    }
}