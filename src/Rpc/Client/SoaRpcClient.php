<?php
/**
 * Soa的RPC调用客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 13:48
 */

namespace Rpc\Client;


use Message\Payload\Request;
use Message\Payload\Request_Header;
use Message\Payload\Response;
use Message\Payload\Response_Header;
use Rpc\Autoloader;
use Rpc\Exceptions\MessageException;
use Rpc\Transport\Stream\TcpStream;

class SoaRpcClient extends AbstractRpcClient
{
    use SoaRpcTrait;

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
     *
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
     * SoaRpcClient constructor.
     *
     * @param string $remoteIp
     * @param int $port
     * @param int $connectTimeout
     */
    public function __construct($remoteIp , $port , $connectTimeout)
    {
        //transport init
        $this->transport = new TcpStream($remoteIp, $port, $connectTimeout);

        //autoloader
        $autoloader = new Autoloader();
        $autoloader->register();

        //request message header|body init
        $this->rpcRequestHeader = new Request_Header();
        $this->rpcRequest = new Request();

        //response message header|body init
        $this->rpcResponseHeader = new Response_Header();
        $this->rpcResponse = new Response();

        //autoloader unregister
        $autoloader->unRegister();
    }

    /**
     * 初始化SOA RPC的请求头部
     *
     * @param array $requestHeader
     * @return $this
     */
    public function initRequestHeader($requestHeader = [])
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

        return $this;
    }

    /**
     * 调取SOA服务
     *
     * @param string $method
     * @param array $body
     * @param string $server
     * @return \stdClass | false 成功返回对应的RPC调用接口
     */
    public function call($method = '', $body = [], $server = '')
    {
        $reqPackedData = $this->pack($method, $body, $server);

        $respPackedData = $this->transport->writeGetRead($reqPackedData);

        return $this->unpack($respPackedData);
    }

    /**
     * SOA服务的相关数据基于Protobuf进行数据封包
     *
     * @param string $method
     * @param array $body
     * @param string $server
     * @return string
     */
    protected function pack($method = '', $body = [], $server = '')
    {
        //change request header
        $this->rpcRequestHeader
            ->setService($server)
            ->setMethod($method);

        //fill body
        $bodyString = is_array($body) ? json_encode($body) : json_encode([$body]);
        $this->rpcRequest->setHeader($this->rpcRequestHeader)->setBody($bodyString);

        //patch message
        $protobufRawString = $this->rpcRequest->serializeToString();

//        $this->testRequest($protobufRawString);

        //obs handle
        return TRANSPORT_TYPE == 'OBS' ?
                $this->getByte($this->rpcRequest->byteSize()). $protobufRawString : //obs patch (后续需要fix掉的)
                $protobufRawString
            ;
	}

	public function testRequest($data){
        $request = new Request();
        $request->mergeFromString($data);
        var_dump($request->getBody(), $request->getHeader());
        exit;
    }

    public function testResponse($data) {
	    $response = new Response();
	    $response->mergeFromString($data);
	    var_dump($response->getBody(),$response->getHeader());
    }

    /**
     * obs的获取二进制字符串前面加上其长度算法字节
     *
     * @param string $value
     * @return string
     */
    protected function getByte($value)
    {
        $dataLengthByte = '';
        while (true){
            if (($value & ~0x7F) == 0) {
                $dataLengthByte .= pack('c', $value);
                return $dataLengthByte;
            } else {
                $b = (($value & 0x7F) | 0x80);
                $dataLengthByte .= pack('c', $b);
                $value = $value >> 7;
            }
        }
    }

    /**
     * SOA服务的相关数据基于Protobuf进行数据解封
     *
     * @param string $data
     *
     * @return \ArrayObject|mixed
     * @throws MessageException
     */
    protected function unpack($data = '')
    {
        try{
            if (empty($data)) {
                throw new \Exception('MESSAGE TO UNPACK IS EMPTY !!');
            }

            //unpack protobuf stream
            $rpcResponse = new Response();
            $rpcResponse->mergeFromString($data);
            $responseHeader = $this->rpcResponse->getHeader();
            $responseBody = $this->rpcResponse->getBody();

            return [
                'header' => [
                    'code'    => $responseHeader->getCode(),
                    'msg'     => $responseHeader->getMessage(),
                    'mid'     => $responseHeader->getMId(),
                    'success' => $responseHeader->getSuccess(),
                ],
                'body' => json_decode($responseBody, true) ?? false,
            ];
        }catch (\Exception $e){
            //log error response data

            throw new MessageException(sprintf('%s %s', $e->getMessage(), $data));
        }
    }

}