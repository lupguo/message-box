<?php
/**
 * RPC 流处理传输层类
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport\Stream;

use Rpc\Exceptions\TransportException;
use Rpc\Transport\InterfaceTransport;

class StreamTransport implements InterfaceTransport {

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
	 * 构建连接资源
	 *
	 * @param string $remoteIp
	 * @param int $port
	 * @param int $connectTimeout
	 * @throws TransportException
	 */
	public function __construct($remoteIp = '127.0.0.1', $port = 43210, $connectTimeout = 3)
	{
    	$remoteSocket = sprintf("tcp://%s:%d", $remoteIp, $port);

    	$this->resource = @stream_socket_client($remoteSocket, $errno, $errstr, $connectTimeout);

		if ($this->resource === false) {
			throw new TransportException(sprintf("STREAM SOCKET CLIENT CREATED ERROR ON %s (%d): %s .", $remoteSocket, $errno, $errstr));
		}
	}

    /**
     * 设置当前操作流的资源
     *
     * @param $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * 写入数据到资源流中
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
     * @throws TransportException
     */
    public function readData()
    {
        if ( !is_resource($this->resource) ) {
            throw new TransportException("IN RPC TRANSPORT , READ RESOURCE IS UNAVAILABLE !!");
        }
    
        $readData = '';
        while (!feof($this->resource)) {
            $readData .= fread($this->resource, $this->readBuffer);
        }
        
        return $readData;
    }

    /**
     * 写入数据到流中，并从流中读取响应数据
     *
     * @param $data
     *
     * @return string
     */
    public function writeAndRead($data)
    {
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
    public function close($resource)
    {
        return fclose($resource);
    }
}