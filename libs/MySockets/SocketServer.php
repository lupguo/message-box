<?php

namespace MySockets;

/**
 * Socket 服务
 *
 * @author  Terry (psr100)
 * @date    2017/11/9
 * @since   2017/11/9 11:12
 */
class SocketServer implements InterfaceServer
{
	CONST BLOCK 	= 1;
	CONST UNBLOCK	= 2;

	/**
	 * 监听的服务器IP
	 *
	 * @var string
	 */
	private $serverIp = '0.0.0.0';

	/**
	 * 监听的服务器Port
	 *
	 * @var int
	 */
	private $serverPort = 43210;

	/**
	 * 当前客户端连接资源
	 *
	 * @var array
	 */
	private $clientSockets = [];

	/**
	 * Socket资源
	 *
	 * @var resource
	 */
	private $serverSocket;

	/**
	 * Socket服务初始化相关
	 *
	 * SocketServer constructor.
	 *
	 * @param int $socketBlock
	 */
	public function __construct($socketBlock = self::BLOCK)
	{
		set_time_limit ( 0 );

		try {
			//扩展检测
			if (!extension_loaded('sockets')) {
				$this->triggerThrowException('The sockets extension is not loaded !!');
			}

			//创建
			if ( false === ($this->serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) ) {
				$this->triggerThrowException("socket_create() failed !!");
			}

			//设置属性
			if ( false === socket_set_option( $this->serverSocket, SOL_SOCKET, SO_REUSEADDR, 1) ) {
				$this->triggerThrowException("socket_set_option() failed !!");
			}

			//绑定
			if ( false === socket_bind($this->serverSocket, $this->serverIp, $this->serverPort)) {
				$this->triggerThrowException("socket_bind() failed !!");
			}

			//监听
			if ( false === socket_listen($this->serverSocket, 5)) {
				$this->triggerThrowException("socket_listen() failed !!");
			}

//			//设置非阻塞
//			if ( $socketBlock == self::UNBLOCK && false == socket_set_nonblock($this->serverSocket)) {
//				$this->triggerThrowException("socket_set_nonblock() failed !!");
//			}
			socket_set_nonblock($this->serverSocket);



		}catch (\Exception $e) {
			printf("Server start false !! %s", $e->getMessage());
		}

	}

	/**
	 * 开启Socket服务
	 *
	 */
	public function start() {
		//服务端启动提示
		$this->serverStartPrompt();

		do {
			if(($newc = socket_accept($this->serverSocket)) !== false)
			{
				var_dump($newc);
				echo "Client $newc has connected..\n";
				$clients[] = $newc;
			}

			usleep(500000);
		}while(true);

		do {
			if ( false !== ($newc = socket_accept($this->serverSocket)) ){
				$this->triggerThrowException("socket_accept() failed !!");
			}
			$this->clientSockets[] = $newc;

			//客户端欢迎词
			if( false === socket_write($newc, $this->clientWelcome()) ) {
				$this->triggerThrowException("socket_write() failed !!");
				break;
			}

			//与客户端交互
			do {

				//按行读取
				if ( false === ($buf = socket_read($newc, 1024,  PHP_BINARY_READ )) ) {
					$this->triggerThrowException("socket_read() from cleint failed !!");
				}

				//输入内容检测
				if ( empty($buf = trim($buf)) ) {
					continue;
				}

				//基于客户端输入，做对应处理
				switch ($buf) {

					//退出客户端连接
					case 'quit' : {
						if (false !== socket_write($newc, "GoodBye !!\n")) {
							socket_close($newc);
							break 2;
						}
					}

					//关闭服务连接
					case 'shutdown' : {
						if (false !== socket_write($newc, "Shutdown Server !!\n")) {
							socket_close($newc);
							socket_close($this->serverSocket);
							break 3;
						}
					}

					//服务连接状态
					case 'status' : {
						$status = [
							'server'	=> $this->serverSocket,
							'clients'	=> $this->clientSockets,
							'server_status' 	=> stream_get_meta_data($this->serverSocket),
							'clients_status'	=> stream_get_meta_data($newc),
						];

						var_dump($status);
						break;
					}

					default : {
						$this->work($newc, $buf);
					}
				}

			}while(true);

		}while (true);
	}

	/**
	 * 服务端欢迎词
	 *
	 * @return void
	 *
	 */
	private function serverStartPrompt() {
		printf("Server started by php socket, Listen on %s:%s ... \n", $this->serverIp, $this->serverPort);
	}

	/**
	 * 客户端欢迎词
	 *
	 * @return string
	 */
	private function clientWelcome() {
		return sprintf("Welcome to the PHP Test Server. \nTo quit, type 'quit'. To shut down the server type 'shutdown'.\n");
	}

	/**
	 * 服务端的工作
	 *
	 * @param $newc
	 * @param $buf
	 */
	private function work($newc, $buf) {
		//show to server
		printf("Client input : %s \n", $buf);

		//return to input
		socket_write($newc, sprintf("[%s server back] %s \r\n", date('Y-m-d H:i:s'), $buf));

	}

	/**
	 * 触发异常信息
	 *
	 * @param string $errorMsg 异常消息
	 * @throws \Exception
	 */
	private function triggerThrowException($errorMsg = '') {
		if (is_resource($this->serverSocket)) {
			$errorMsg = socket_strerror(socket_last_error($this->serverSocket));
		}

		throw new \Exception(sprintf('[Server Error]: %s', $errorMsg), -500);
	}

	/**
	 * 释放相关占用的资源
	 *
	 */
	public function __destruct()
	{
		foreach (($this->clientSockets + [$this->serverSocket]) as $fd) {
			if (is_resource($fd)) {
				printf("destruct: release socket!");
				socket_close($fd);
			}
		}
	}


}