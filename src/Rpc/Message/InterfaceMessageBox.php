<?php
/**
 * 消息接口
 *
 * @author  Terry (psr100)
 * @date    2017/11/17
 * @since   2017/11/17 16:29
 */

namespace Rpc\Message;


interface InterfaceMessageBox
{

    /*
     * 消息封包
     *
     */
    public function pack();

    /**
     * 消息解包
     *
     * @return mixed
     */
    public function unpack();

}