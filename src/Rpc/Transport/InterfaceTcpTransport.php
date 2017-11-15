<?php
/**
 * 传输层相关接口
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport;


interface InterfaceTcpTransport
{
	/**
	 *
	 * 发送TCP请求数据
	 *
	 * @param string $data
	 * @return mixed
	 */
	public function sendRequest($data);

	/**
	 * 获取TCP响应数据
	 *
	 * @return mixed
	 */
	public function getResponse();


}