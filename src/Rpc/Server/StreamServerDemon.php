<?php
/**
 * 简单的模拟Soa服务端输出Protobuf响应
 *
 * @Author  Terry
 * @Date    2017/11/16 0:00
 */

namespace Rpc\Server;


use Message\Payload\Request;
use Message\Payload\Response;
use Message\Payload\Response_Header;
use Rpc\Autoloader;
use Rpc\Transport\Stream\StreamTransport;

class StreamServerDemon extends StreamTransport
{
    /**
     * 资源流
     *
     * @var resource
     */
    private $serverStream;

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
        $this->serverStream = stream_socket_server($localSocket, $errno, $errstr) or die(sprintf("STREAM SERVER CREATE FAILED, %s", $errstr));
    }

    /**
     * 开启服务接收客户端请求，并响应对应的Response响应（经过Protobuf处理过的）
     *
     * @param int    $status
     * @param string $message
     * @param array  $body
     */
    public function start($status = 200, $message = '', $body = [])
    {
        //client accept
        while ($clientSocket = stream_socket_accept($this->serverStream, -1, $peer)) {
            if ($clientSocket === false) {
                die("CLIENT SOCKET ACCEPT ERROR !!");
            }
            //read request
            printf("CLIENT FROM: [ %s ] \n", $peer);

            //set current r/w stream
            $this->setResource($clientSocket);
            $this->writeData($this->getResponseData($status, $message, $body));

            //close resource
            $this->close($clientSocket);
        }
    }

    /**
     * 打印用户的输入内容
     */
    public function dumpReceiveData()
    {
        $rpcRequest = new Request();
        $rpcRequest->mergeFromString($this->readData());
        var_dump([
            'header' => $rpcRequest->getHeader(),
            'body'   => $rpcRequest->getBody(),
        ]);
    }

    /**
     * 获取响应给客户端的数据
     *
     * @param int    $status
     * @param string $message
     * @param array  $body
     *
     * @return string
     */
    private function getResponseData($status = 200, $message = '', $body = [])
    {
        //response header
        $messageRespHeader = new Response_Header();
        $messageRespHeader
            ->setCode($status)
            ->setMessage($message)
            ->setMId(1)
            ->setSuccess(1);

        //response body
        $body            = is_array($body) ? $body : [$body];
        $body            = json_encode($body);
        $messageResponse = new Response();
        $messageResponse
            ->setHeader($messageRespHeader)
            ->setBody($body);

        //response data
        return $messageResponse->serializeToString();
    }
}