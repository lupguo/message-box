<?php
/**
 * 传输层相关抽象
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 18:49
 */

namespace Rpc\Transport;


use Message\Payload\Response;
use Rpc\Client\SoaRpcTrait;
use Rpc\Exceptions\TransportException;

Abstract class AbstractTcpTransport implements InterfaceTcpTransport {

    use SoaRpcTrait;

	/**
	 * 连接的资源对象 Stream|Socket
	 *
	 * @var mixed
	 */
	public $resource;
    
    /**
     * 读的buffer
     *
     * @var integer
     */
	protected $readBuffer = 8196;
    
    /**
     * 写的buffer
     *
     * @var integer
     */
	protected $writeBuffer = 4096;
	
	/**
     * 子类需要构建连接资源
     *
     * AbstractTcpTransport constructor.
	 */
	abstract protected function __construct();
    
    /**
     * 设置当前操作流的资源
     *
     * @param $resource
     *
     * @return $this
     */
	public function setResource($resource) {
	    $this->resource = $resource;
	    
	    return $this;
    }
	
    /**
     * 基于流写入数据
     *
     * @param string $data TCP发送的数据
     *
     * @return string
     * @throws TransportException
     */
	public function writeData($data)
	{
        if ( !is_resource($this->resource) ) {
            throw new TransportException("IN RPC TRANSPORT , WRITE RESOURCE IS UNAVAILABLE !!");
        }
        
		if (empty($data))
			throw new TransportException("EMPTY REQUEST BODY .");

		for ($written = 0; $written < strlen($data); $written += $fwrite) {
			$fwrite = fwrite($this->resource, substr($data, $written), $this->writeBuffer);
			if ($fwrite === false) {
				throw new TransportException(sprintf("SEND REQUEST BODY ERROR ."));
			}
		}
	}
    
    /**
     * 从当前流中读取数据
     *
     * @return string
     * @throws TransportException
     */
	public function readData()
	{
	    if ( !is_resource($this->resource)) {
	        throw new TransportException("IN RPC TRANSPORT , READ RESOURCE IS UNAVAILABLE !!");
        }

        //origin
		$freadData = '';
		while (!feof($this->resource)) {
            $freadData .= fread($this->resource, $this->readBuffer);
		}

		return $freadData;

        //just for soa data read handler
//        $freadData = $this->readSoaData($this->resource);

        $freadData = stream_get_contents($this->resource, 8196);

        $rpcResponse = new Response();
        $rs = $rpcResponse->mergeFromString($freadData);

	    $this->readSoaData($this->resource);


	    //origin
//		$freadData = '';
//		while (!feof($this->resource)) {
//            $freadData .= fread($this->resource, $this->readBuffer);
//		}

        $this->close($this->resource);

        return $freadData;
    }
    
    /**
     * 写入数据到流中，并从流中读取响应数据
     *
     * @param $data
     *
     * @return string
     */
	public function writeGetRead($data) {

	    $this->writeData($data);
	    
	    return $this->readData();
    }
    
    /**
     * 关闭当前资源
     *
     * @param $resource
     *
     * @return bool
     */
    public function close($resource) {
	    return fclose($resource);
    }
}