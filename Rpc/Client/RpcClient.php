<?php
/**
 * 基于Stream & Protobuf构建的Rpc客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 11:27
 */

namespace Rpc;


use Message\Playload\Request;
use Message\Playload\Request_Header;
use Message\Playload\Response;
use Rpc\Transport\Stream\TcpStream;

class RpcClient implements InterfaceWorker
{
	/**
	 * 传输体
	 *
	 */
	public $transport;

    /**
     * 传输的消息体
     *
     * @var
     */
	private $message;

	/**
	 *
	 * RpcClient constructor.
	 */
	public function __construct()
	{
		$this->transport = new TcpStream();
	}

	public function sendRequest(Request $messageRequest) {

		$message = $this->transport->sendRequest($messageRequest);

		$response = $this->parseMessage($message);

		return $this->getResponse();
		return null;
	}

    /**
     * 返回解析后的消息解析消息
     * 
     * @param $message
     * @return mixed
     */
	public function parseMessage($message) {
        $respMessage = new Response();
        return $respMessage->mergeFromString($message);
        var_dump($respMessage->getBody(), json_decode($respMessage->getBody(), 1));
	}

	public function pack($body = [])
	{
        //header
        $reqHeader	= new Request_Header();
        $reqHeader
            ->setTokenId('a7f1db0a670e3c3cabf81b62975f5891')
            ->setVersion('1.0.0')
            ->setService('com.globalegrow.spi.mpay.inter.PaySystemService')
            ->setMethod('queryLoginfo')
            ->setDomain('')
            ->setMId('')
            ->setType(1)
            ->setUrl('')
        ;

        //body
        $reqMessage = new Request();
        $reqMessage
            ->setHeader($reqHeader)
            ->setBody($body)
        ;
        $reqMessage->setBody(json_encode($body));

        //pack
        $byteSize = $reqMessage->byteSize();

        //serialize to string
        $serString = $reqMessage->serializeToString();
        $serJsonString = $reqMessage->serializeToJsonString();
	}

	public function unpack($data)
	{
		// TODO: Implement unpack() method.
	}

}