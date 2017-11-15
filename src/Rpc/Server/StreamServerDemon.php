<?php
/**
 * 简单的模拟Soa服务端输出Protobuf响应
 *
 * @Author  Terry
 * @Date    2017/11/16 0:00
 */

namespace Rpc\Server;


use Message\Payload\Response;
use Message\Payload\Response_Header;
use Rpc\Autoloader;

class StreamServerDemon
{
    /**
     * 服务流资源
     *
     * @var resource
     */
    private $stream;
    
    /**
     * StreamServerDemon constructor.
     */
    public function __construct()
    {
        //autoloader
        $autoloader = new Autoloader();
        $autoloader->register();
        
        //stream socket listen
        $localSocket = 'tcp://192.168.10.10:43217';
        printf("LISTEN ON : [ %s ]\n", $localSocket);
        $this->stream = stream_socket_server($localSocket, $errno, $errstr)
            or die(sprintf("stream server create failed, %s", $errstr));
    }
    
    /**
     * 开启服务接收客户端请求，并响应对应的Response响应（经过Protobuf处理过的）
     *
     * @param int    $status
     * @param string $message
     * @param array  $body
     */
    public function start($status = 200, $message='', $body = []){
        //response header
        $messageRespHeader = new Response_Header();
        $messageRespHeader
            ->setCode($status)
            ->setMessage($message)
            ->setMId(1)
            ->setSuccess(1)
        ;
        
        //response body
        $body = is_array($body) ? $body : [$body];
        $body = json_encode($body);
        $messageResponse = new Response();
        $messageResponse
            ->setHeader($messageRespHeader)
            ->setBody($body);
        
        //response data
        $sendData = $messageResponse->serializeToString();
        
        while ($clientSocket = stream_socket_accept($this->stream, -1, $peer)) {
            printf("CLIENT FROM: [ %s ] \n", $peer);
    
            //一次写入大于4k的响应，分次写入buffer
            for ($written = 0; $written < strlen($sendData); $written += $fwrite) {
                $fwrite = fwrite($clientSocket, substr($sendData, $written), 4096);
                if ($fwrite === false) {
                    die("SEND RESPONSE BODY ERROR .");
                }
            }
            fclose ($clientSocket);
        }
    }
    
    public function dump() {
    
    }
}