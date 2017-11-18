<?php
/**
 * SOA - 网站对接使用的MessageBox
 *
 * @author  Terry (psr100)
 * @date    2017/11/17
 * @since   2017/11/17 16:31
 */

namespace Rpc\Message;


use Message\Payload\Request;
use Message\Payload\Request_Header;
use Message\Payload\Response;
use Message\Payload\Response_Header;
use Rpc\Autoloader;
use Rpc\Exceptions\MessageException;

class SoaMessageBox implements InterfaceMessageBox
{

    /**
     * Soa服务
     *
     * @var string
     */
    private $server;

    /**
     * SOA服务接口
     *
     * @var string
     */
    private $method;

    /**
     * 接口版本
     *
     * @var string
     */
    private $version;

    /**
     * rpc请求类型 1：为数据 2：心跳
     *
     * @var int
     */
    private $type = 1;

    /**
     * 接口TokenID
     *
     * @var string
     */
    private $tokenId;

    /**
     * 站点编码
     *
     * @var string
     */
    private $siteCode;

    /**
     * 业务ID
     *
     * @var string
     */
    private $mid;

    /**
     * 服务域
     *
     * @var string
     */
    private $domain = '';

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var Request_Header
     */
    private $rpcRequestHeader;

    /**
     * @var Request
     */
    private $rpcRequest;

    /**
     * @var Response_Header
     */
    private $rpcResponseHeader;

    /**
     * @var Response
     */
    private $rpcResponse;

    /**
     * SoaMessage constructor.
     *
     * @param array $requestHeader
     */
    public function __construct($requestHeader = [])
    {
        //初始化protobuf
        $this->initPbf();

        //初始化protobuf的请求头
        $this->initPbfRequestHeader($requestHeader);
    }

    /**
     * 利用Protobuf压缩传输协议相关初始化
     */
    private function initPbf()
    {
        //autoloader
        $autoloader = new Autoloader();
        $autoloader->register();

        //request message header|body init
        $this->rpcRequestHeader = new Request_Header();
        $this->rpcRequest       = new Request();

        //response message header|body init
        $this->rpcResponseHeader = new Response_Header();
        $this->rpcResponse       = new Response();

        //autoloader unregister
        $autoloader->unRegister();
    }

    /**
     * 初始化SOA RPC的请求头部
     *
     * @param array $requestHeader
     *
     * @return $this
     */
    private function initPbfRequestHeader($requestHeader = [])
    {

        list($this->tokenId, $this->version, $this->type, $this->mid, $this->domain, $this->url) = [
            isset($requestHeader['tokenId']) ? $requestHeader['tokenId'] : $this->tokenId,
            isset($requestHeader['version']) ? $requestHeader['version'] : $this->version,
            isset($requestHeader['type']) ? $requestHeader['type'] : $this->type,
            isset($requestHeader['mid']) ? $requestHeader['mid'] : $this->mid,
            isset($requestHeader['domain']) ? $requestHeader['domain'] : $this->domain,
            isset($requestHeader['url']) ? $requestHeader['url'] : $this->url,
        ];

        $this->rpcRequestHeader
            ->setTokenId($this->tokenId)
            ->setVersion($this->version)
            ->setDomain($this->domain)
            ->setMId($this->mid)
            ->setType($this->type)
            ->setUrl($this->url)
        ;
    }


    /**
     * SOA服务的相关数据基于Protobuf进行数据封包
     *
     * @param string $method
     * @param array  $body
     * @param string $server
     *
     * @return string length内容+protobuf处理过后的soa服务的消息体(已转成对应的协议字符内容)
     */
    public function pack($method = '', $body = [], $server = '')
    {
        //change request header
        $this->rpcRequestHeader
            ->setService($server)
            ->setMethod($method)
        ;

        //fill body
        $bodyString = is_array($body) ? json_encode($body) : json_encode([$body]);
        $this->rpcRequest->setHeader($this->rpcRequestHeader)->setBody($bodyString);

        //patch message
        $protobufRawString = $this->rpcRequest->serializeToString();

        return $protobufRawString;
    }

    /**
     * SOA服务的相关数据基于Protobuf进行数据解封
     *
     * @param string $data
     *
     * @return \ArrayObject|mixed
     * @throws MessageException
     */
    public function unpack($data = '')
    {
        try {
            if (empty($data)) {
                throw new \Exception('MESSAGE TO UNPACK IS EMPTY !!');
            }

            //unpack protobuf stream
            $this->rpcResponse->mergeFromString($data);
            $responseHeader = $this->rpcResponse->getHeader();
            $responseBody   = $this->rpcResponse->getBody();

            return [
                'header' => [
                    'code'    => $responseHeader->getCode(),
                    'msg'     => $responseHeader->getMessage(),
                    'mid'     => $responseHeader->getMId(),
                    'success' => $responseHeader->getSuccess(),
                ],
                'body'   => json_decode($responseBody, true) ?? false,
            ];
        } catch (\Exception $e) {
            //log error response data
            throw new MessageException(sprintf('%s %s', $e->getMessage(), $data));
        }
    }
}