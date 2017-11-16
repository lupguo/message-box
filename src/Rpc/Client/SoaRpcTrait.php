<?php
/**
 * Obs的中途的一些数据处理方式。
 *
 * @author  Terry (psr100)
 * @date    2017/11/16
 * @since   2017/11/16 10:19
 */

namespace Rpc\Client;


use Google\Protobuf\Internal\CodedInputStream;
use Google\Protobuf\Internal\GPBWire;
use Rpc\Exceptions\MessageException;

trait SoaRpcTrait
{

    /**
     * obs的数据读取过程，这块后续需要fix掉
     *
     * @param $resource
     * @return string
     *
     * @throws MessageException
     */
    protected function readSoaData($resource) {

        #OBS设定的数据长度最大字节数
        $MAX_BYTE_COUNT = 5;

        $content= fread($resource, $MAX_BYTE_COUNT);
        $length = $content;
        if (!empty($length)) {
            $input = new CodedInputStream($length);
            $input->readVarint32($length);
            $lengthByteCount = GPBWire::varint32Size($content);
            $length = $length - $MAX_BYTE_COUNT + $lengthByteCount;
        } else {
            throw new MessageException('read tag length error');
        }
        $content .= stream_get_contents($resource, $length);

        return $content;
    }


}