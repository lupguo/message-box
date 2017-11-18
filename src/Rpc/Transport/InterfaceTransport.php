<?php
/**
 * 传输层相关接口
 *
 * @author  Terry (psr100)
 * @date    2017/11/14
 * @since   2017/11/14 16:53
 */

namespace Rpc\Transport;


interface InterfaceTransport
{
    /**
     * 基于TCP连接，写入数据到缓存区中
     *
     * @param string $data TCP发送的数据
     */
    public function writeData($data);

    /**
     * 基于TCP连接，从数据缓存区中读取数据
     */
    public function readData();


}