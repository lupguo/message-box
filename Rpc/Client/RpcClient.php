<?php
/**
 * 基于Stream & Protobuf构建的Rpc客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 11:27
 */

namespace Rpc;


use Message\Playload\Request as MessageRequest;
use Message\Playload\Response as MessageResponse;
use Rpc\Transport\Stream\TcpStream;

class RpcClient implements InterfaceWorker
{
	/**
	 * 传输体
	 *
	 */
	public $transport;

	/**
	 *
	 * RpcClient constructor.
	 */
	public function __construct()
	{
		$this->transport = new TcpStream();
	}

	/**
	 * 发送RPC请求
	 *
	 * @param MessageRequest $messageRequest
	 */
	public function sendRequest(MessageRequest $messageRequest) {

		$data = $this->transport->sendRequest($messageRequest);

		$response = new MessageResponse();

		return $this->getResponse();
		return null;
	}


	/**
	 * 获取PRC响应
	 *
	 * @param MessageResponse $response
	 */
	public function getResponse(MessageResponse $response) {

	}

	/**
	 * 封包
	 *
	 * @param $data
	 */
	public function pack($data)
	{
		// TODO: Implement pack() method.
	}

	/**
	 * 解封
	 *
	 * @param $data
	 */
	public function unpack($data)
	{
		// TODO: Implement unpack() method.
	}

}