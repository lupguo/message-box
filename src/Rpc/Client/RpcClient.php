<?php
/**
 * RPC客户端
 *
 * @author  Terry (psr100)
 * @date    2017/11/15
 * @since   2017/11/15 13:48
 */

namespace Rpc\Client;

use Rpc\Message\InterfaceMessageBox;
use Rpc\Message\SoaMessageBox;
use Rpc\Transport\InterfaceTransport;
use Rpc\Transport\Stream\StreamTransport;

class RpcClient
{
    /**
     * 传输方式
     *
     * @var StreamTransport
     */
    private $transport;

    /**
     * 传输的消息
     *
     * @var SoaMessageBox
     */
    private $messageBox;

    /**
     * RpcClient constructor.
     *
     * @param InterfaceTransport  $transport
     * @param InterfaceMessageBox $messageBox
     */
    public function __construct(InterfaceTransport $transport, InterfaceMessageBox $messageBox)
    {
        //transport init
        $this->transport = $transport;

        //messagebox init
        $this->messageBox = $messageBox;
    }

    /**
     * 调取SOA服务
     *
     * @param string $method        调用SOA方法名
     * @param string $version       调用SOA方法使用的版本
     * @param array  $body          调用SOA方法的请求数据
     * @param string $server        SOA服务名称
     *
     * @return false|\stdClass 成功返回对应的RPC调用接口
     */
    public function call($method = '', $version = '', $body = [], $server = '')
    {
        $reqPackedMessage = $this->messageBox->pack($method, $version, $body, $server);

        $respPackedData = $this->transport->writeAndRead($reqPackedMessage);

        return $this->messageBox->unpack($respPackedData);
    }
}