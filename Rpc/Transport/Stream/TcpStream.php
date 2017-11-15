<?php
/**
 * RPC 流处理客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport\Stream;


use Message\Playload\Request;
use Message\Playload\Request_Header;
use Rpc\Transport\AbstractTcpTransport;

class TcpStream extends AbstractTcpTransport {

    public function __construct(){

    	$this->connect('', '');

	}

    public function connect($hostIp = '127.0.0.1', $hostPort = 43210)
    {

    }

    public function sendRequest($message = '')
    {

    }

    public function getResponse()
	{
		// TODO: Implement getResponse() method.
	}

}