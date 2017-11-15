<?php
/**
 * 基于Stream & Protobuf构建的Rpc客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 11:27
 */

namespace Rpc\Client;


use Rpc\Transport\Stream\TcpStream;

abstract class AbstractRpcClient
{
	/**
	 * 传输体
	 *
	 */
	protected $transport;

	/**
	 * RpcClient constructor.
	 *
	 * @param string $remoteIp
	 * @param int $port
	 * @param int $connectTimeout
	 */
	public function __construct($remoteIp , $port , $connectTimeout)
	{
		$this->transport = new TcpStream($remoteIp, $port, $connectTimeout);
	}

	/**
	 * 数据封装
	 *
	 * @param $data
	 * @return mixed
	 */
	abstract protected function pack($data);

	/**
	 * 数据解封
	 *
	 * @param $data
	 * @return mixed
	 */
	abstract protected function unpack($data);

}