<?php
/**
 * Soa对应的流处理传输层类
 *
 * @Author  Terry
 * @Date    2017/11/18 1:23
 */

namespace Rpc\Transport\Stream;


use Google\Protobuf\Internal\GPBWire;
use Google\Protobuf\Internal\InputStream;
use Rpc\Exceptions\TransportException;

class SoaStreamTransport extends StreamTransport
{
    /**
     * SoaStreamTransport constructor.
     *
     * @param string $remoteIp
     * @param int $port
     * @param int $connectTimeout
     */
    public function __construct($remoteIp = '127.0.0.1', $port = 43210, $connectTimeout = 3)
    {
        parent::__construct($remoteIp, $port, $connectTimeout);
    }

    /**
     * 模拟Obs从流中读取对应的资源
     *
     * @throws TransportException
     */
    public function readData()
    {
        if (!is_resource($this->resource)) {
            throw new TransportException("IN RPC TRANSPORT , READ RESOURCE IS UNAVAILABLE !!");
        }

        $maxByteSize = 5;
        $length = 0;
        $content = fread($this->resource, $maxByteSize);
        if (!empty($content)) {
            $input = new InputStream($content);
            $input->readVarint32($length);
            $lengthByteCount = GPBWire::varint32Size($length);
            $length = $length - $maxByteSize + $lengthByteCount;
        } else {
            throw new TransportException('IN RPC TRANSPORT , READ TAG LENGTH ERROR !!');
        }
        $content .= stream_get_contents($this->resource, $length);

        return $content;
    }


    /**
     * 安装SOA对接流规则，在传输数据前追加对应的的字节长度
     *
     * @param string $data TCP发送的数据
     *
     * @return string
     * @throws TransportException
     */
    public function writeData($data)
    {
        if (!is_resource($this->resource)) {
            throw new TransportException("IN RPC TRANSPORT , WRITE RESOURCE IS UNAVAILABLE !!");
        }

        if (empty($data))
            throw new TransportException("EMPTY REQUEST BODY .");

        //add pack length
        $data = $this->encodeLength(strlen($data)) . $data;

        for ($written = 0; $written < strlen($data); $written += $fwrite) {
            $fwrite = fwrite($this->resource, substr($data, $written), $this->writeBuffer);
            if ($fwrite === false) {
                throw new TransportException(sprintf("SEND REQUEST BODY ERROR ."));
            }
        }
    }

    /**
     * obs的获取二进制字符串前面加上其长度算法字节
     *
     * @param string $value
     * @return string
     */
    private function encodeLength($value)
    {
        $dataLengthByte = '';
        while (true) {
            if (($value & ~0x7F) == 0) {
                $dataLengthByte .= pack('c', $value);
                break;
            } else {
                $b = (($value & 0x7F) | 0x80);
                $dataLengthByte .= pack('c', $b);
                $value = $value >> 7;
            }
        }

        return $dataLengthByte;
    }
}