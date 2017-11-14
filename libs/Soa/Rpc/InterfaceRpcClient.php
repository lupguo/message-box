<?php
/**
 * Rpc调用的客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Soa\Rpc;


interface InterfaceRpcClient
{
	/**
	 * 连接RPC的服务
	 *
	 * @return mixed
	 */
	public function connect();

	/**
	 *
	 * 发送RPC请求数据
	 *
	 * @param $requestContent
	 * @return mixed
	 */
	public function sendRequest($requestContent);

	/**
	 * 获取RPC响应数据
	 *
	 * @return mixed
	 */
	public function getResponse();


}