<?php
/**
 * RPC 流处理客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport\Stream;

use Google\Protobuf\Internal\GPBWire;
use Google\Protobuf\Internal\InputStream;
use Rpc\Exceptions\TransportException;
use Rpc\Transport\InterfaceTransport;

class StreamTranstport implements InterfaceTransport {

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
     * 从当前流中读取数据，读取的具体长度在流前指定字节已经指名清楚了
     *
     * @throws TransportException
     */
    public function readData()
    {
        //like obs soa stream read
        return $this->soaReadData($this->resource);
    }

    /**
     * 模拟Obs从流中读取对应的资源
     *
     * @param null $resource
     * @return bool|string
     * @throws TransportException
     */
    public function soaReadData($resource = null)
    {
        if ( !is_resource($resource)) {
            throw new TransportException("IN RPC TRANSPORT , READ RESOURCE IS UNAVAILABLE !!");
        }

        $maxByteSize = 5;
        $length = 0;
        $content = fread($resource, $maxByteSize);
        if (!empty($content)) {
            $input = new InputStream($content);
            $input->readVarint32($length);
            $lengthByteCount = GPBWire::varint32Size($length);
            $length = $length - $maxByteSize + $lengthByteCount;
        } else {
            throw new TransportException('IN RPC TRANSPORT , READ TAG LENGTH ERROR !!');
        }
        $content .= stream_get_contents($resource, $length);

        return $content;
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