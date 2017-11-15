<?php
/**
 * 传输层相关抽象
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 18:49
 */

namespace Rpc\Transport;


use Rpc\Exceptions\TransportException;

Abstract class AbstractTcpTransport implements InterfaceTcpTransport {

	/**
	 * 客户端连接的资源对象 Stream|Socket
	 *
	 * @var mixed
	 */
	protected $resource;

	/**
	 * 子类需要构建连接资源
	 *
	 * AbstractTcpTransport constructor.
	 */
	abstract protected function __construct();

	/**
	 * 发送TCP的请求数据
	 *
	 * @param string $message 发送的数据
	 * @param int $written 每次写入BUFF的字节数，当发送数据大于此数据，将拆分发送
	 * @return bool|int
	 * @throws TransportException
	 */
	public function sendRequest($message = '', $written = 4096)
	{
		if (empty($message))
			throw new TransportException("EMPTY REQUEST BODY .");

		for ($written = 0; $written < strlen($message); $written += $fwrite) {
			$fwrite = fwrite($this->resource, substr($message, $written));
			if ($fwrite === false) {
				throw new TransportException(sprintf("SEND REQUEST BODY ERROR ."));
			}
		}

		return  $this->getResponse() ;
	}

	/**
	 * 返回当前TCP响应的数据
	 *
	 * @return string
	 */
	public function getResponse()
	{
		$fread = '';
		while (!feof($this->resource)) {
			$fread .= fread($this->resource, 4096);
		}

		return $fread;
	}
}