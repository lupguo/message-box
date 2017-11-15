<?php
/**
 * Your file description.
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 13:48
 */

namespace Rpc\Client;


use Message\Payload\Request;
use Message\Payload\Request_Header;
use Rpc\Autoloader;
use Rpc\Exceptions\MessageException;
use Rpc\Transport\Stream\TcpStream;

class SoaRpcClient extends AbstractRpcClient
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
        
		//message header init
		$this->rpcRequestHeader = new Request_Header();

		//message body init
		$this->rpcRequest = new Request();
		
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
     * @return mixed|void
     */
    public function call($method = '', $body = [], $server = '')
    {
        $requestPack = $this->pack($method, $body, $server);

        return $this->unpack($this->transport->sendRequest($requestPack));
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
		return $this->rpcRequest->serializeToString();
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
            $jsonString = $this->rpcRequest->mergeFromString($data);
            
            var_dump($jsonString);
            exit;
            return json_decode($jsonString) ?? new \ArrayObject();
        }catch (\Exception $e){
            //log error response data
            
	        throw new MessageException(sprintf('%s. %s', $e->getMessage(), $data));
        }
	}

}