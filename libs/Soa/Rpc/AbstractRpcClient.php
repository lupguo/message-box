<?php
/**
 * 抽象RPC客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 18:49
 */

namespace Soa\Rpc;


Abstract class AbstractRpcClient implements InterfaceRpcClient {

	/**
	 * 客户端连接的资源对象
	 *
	 * @var mixed
	 */
	protected $clientResource;

}