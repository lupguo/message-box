<?php
/**
 * Obs的中途的一些数据处理方式。
 *
 * @author  Terry (psr100)
 * @date    2017/11/16
 * @since   2017/11/16 10:19
 */

namespace Rpc\Client;


use Google\Protobuf\Internal\GPBWire;
use Google\Protobuf\Internal\RawInputStream;
use Rpc\Exceptions\MessageException;

trait SoaRpcTrait
{

    /**
     * 参考OBS这块在发送数据之前前还有一个“获取二进制字符串前面加上其长度算法字节”的处理过程
     *
     * @param $data
     * @return string
     */
    protected function beforeSendData($data)
    {
        return $this->getByte(strlen($data)) . $data;
    }

    /**
     * obs的获取二进制字符串前面加上其长度算法字节
     *
     * @param string $value
     * @return string
     */
    protected function getByte($value)
    {
        $dataLengthByte = '';
        while (true){
            if (($value & ~0x7F) == 0) {
                $dataLengthByte .= pack('c', $value);
                return $dataLengthByte;
            } else {
                $b = (($value & 0x7F) | 0x80);
                $dataLengthByte .= pack('c', $b);
                $value = $value >> 7;
            }
        }
    }

    /**
     * obs的数据读取过程
     *
     * @param $resource
     * @return string
     */
    protected function obsReadData($resource) {
        #OBS设定的数据长度最大字节数
        $MAX_BYTE_COUNT = 5;

        $content= $this->read($MAX_BYTE_COUNT);
        $length = $content;
        if (!empty($length)) {
            $input = new RawInputStream($length);
            $input->readVarint32($length);
            $lengthByteCount = GPBWire::varint32Size($length);
            $length = $length - $MAX_BYTE_COUNT + $lengthByteCount;
        } else {
            throw new MessageException('read tag length error');
        }
        $content .= stream_get_contents($resource, $length);

        return $content;
    }


}