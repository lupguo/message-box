<?php
/**
 * RPC - 基础异常
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 11:30
 */

namespace Rpc\Exceptions;


use Throwable;

class RpcException extends \Exception
{
    public function __construct($message = "", $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct(sprintf("SOA RPC RUN AN ERROR !! %s", $message), $code, $previous);
    }
    
}