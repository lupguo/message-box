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

    	$reqMessage = new Request();
		$reqMessage->setHeader($reqHeader);

		//body
		$body = [
			"type" => 1,
			"platform" => 1,
			"pageSize"	=> 20,
			"pageNo"	=> 1,
			"siteCode"	=> "GLB",
		];
		$reqMessage->setBody(json_encode($body));

		//pack
		$byteSize = $reqMessage->byteSize();

		//serialize to string
		$serString = $reqMessage->serializeToString();
		$serJsonString = $reqMessage->serializeToJsonString();

		//serialize to stream|json stream
//		$serToJsonStream = $reqMessage->serializeToJsonStream($jsonStreamOutput);
//		$reqMessage->serializeToStream($steamOutput);

		var_dump($byteSize, $serString, $serJsonString);

		echo 'Unpack ....';
		//unpack string
//		$reqMessage->mergeFromJsonString($serJsonString);
		$reqMessage->mergeFromString($serString);
		var_dump($reqMessage->getBody(), json_decode($reqMessage->getBody(), 1));

		//unpack stream
//		$streamInput = $steamOutput;
//		$jsonStreamInput = $jsonStreamOutput;

//		$parFromStream = $reqMessage->parseFromStream($streamInput);
//		$parFromJsonStream = $reqMessage->parseFromJsonStream($jsonStreamInput);
//		var_dump($parFromStream, $parFromJsonStream);
    }

    /**
     * @param $reqContent
     * @return bool
     */
    public function sendRequest($header = [], $body = '')
    {
		//header
		$reqHeader	= new Request_Header();
		$reqHeader
			->setTokenId($header['tokenId'])
			->setVersion($header['version'])
			->setService('com.globalegrow.spi.mpay.inter.PaySystemService')
			->setMethod('queryLoginfo')
			->setDomain('')
			->setMId('')
			->setType(1)
			->setUrl('')
		;

		//body
		$body = [
			"type" => 1,
			"platform" => 1,
			"pageSize"	=> 20,
			"pageNo"	=> 1,
			"siteCode"	=> "GLB",
		];
		$reqMessage = new Request();
		$reqMessage
			->setHeader($reqHeader)
			->setBody($requestBody)
		;
		$reqMessage->setBody(json_encode($body));

    }

    public function getResponse()
	{
		// TODO: Implement getResponse() method.
	}

}