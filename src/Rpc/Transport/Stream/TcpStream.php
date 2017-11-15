<?php
/**
 * RPC 流处理客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport\Stream;


use Rpc\Exceptions\TransportException;
use Rpc\Transport\AbstractTcpTransport;

class TcpStream extends AbstractTcpTransport {

	/**
	 * stream 资源
	 *
	 * @var resource
	 */
	protected $resource;

	/**
	 * TcpStream constructor.
	 *
	 * @param string $remoteIp
	 * @param int $port
	 * @param int $connectTimeout
	 * @throws TransportException
	 */
	public function __construct($remoteIp = '127.0.0.1', $port = 43210, $connectTimeout = 5)
	{
    	$remoteSocket = sprintf("tcp://%s:%d", $remoteIp, $port);

    	$this->resource = @stream_socket_client($remoteSocket, $errno, $errstr, $connectTimeout);

		if ($this->resource === false) {
			throw new TransportException(sprintf("STREAM SOCKET CLEIENT CREATED ERROR ON %s (%d): %s .", $remoteSocket, $errno, $errstr));
		}
	}
}