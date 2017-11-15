<?php
/**
 * RPC - 传输异常
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 11:33
 */

namespace Rpc\Exceptions;


use Throwable;

class TransportException extends RpcException
{

	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}