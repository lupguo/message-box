<?php
/**
 * 抽象RPC客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 18:49
 */

namespace Rpc\Transport;


Abstract class AbstractTcpTransport implements InterfaceTcpTransport {

	/**
	 * 客户端连接的资源对象
	 *
	 * @var mixed
	 */
	protected $clientResource;

    /**
     * 相关实现方法
     *
     * AbstractRpcClient constructor.
     */
	abstract public function __construct();

}