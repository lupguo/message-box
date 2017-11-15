<?php
/**
 * RPC 流处理客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Soa\Rpc\Streams;


use Soa\Rpc\AbstractRpcClient;

class StreamsClient extends AbstractRpcClient {

    public function __construct(){}

    public function connect($hostIp = '127.0.0.1', $hostPort = 43210)
    {
        // TODO: Implement connect() method.
    }

    /**
     * @param $requestContent
     * @return bool
     */
    public function sendRequest($requestContent)
    {


    }

    public function getResponse()
	{
		// TODO: Implement getResponse() method.
	}

}