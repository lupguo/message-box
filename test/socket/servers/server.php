<?php
/**
 * 服务脚本
 *
 * @author  Terry (psr100)
 * @date    2017/11/9
 * @since   2017/11/9 16:52
 */

Class Server {

	public function __construct()
	{
		$autoloader = require '../vendor/autoload.php';

		$autoloader->addPsr4('', ['../php_out', '../libs']);

		//注册
		register_shutdown_function([$this, 'shutdownCallBack']);
	}

	/**
	 * 服务器正常退出执行的shutdown回调
	 *
	 */
	public function shutdownCallBack() {
		echo "SERVER BYEBYE !!! \n";
	}

	/**
	 * 服务启动
	 *
	 * @param \MySockets\InterfaceServer $server
	 */
	public function run(\MySockets\InterfaceServer $server) {
		$server->start();
	}
}

(new Server())->run(new \MySockets\SocketServer(\MySockets\SocketServer::UNBLOCK));