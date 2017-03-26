<?php

namespace JsonRpcServer\Codec;

use JsonRpcServer\Exception\CodecException;
use JsonRpcServer\ICodec;

/**
 * Class JsonCodec
 * @package JsonRpcServer\Codec
 */
class JsonCodec implements ICodec
{
    /**
     * @param $data
     * @return mixed
     */
    public function decode($data)
    {
        $decoded = json_decode($data, true);

        $this->handleDecodeError();

        return $decoded;
    }

    /**
     * @param $data
     * @return string
     */
    public function encode($data)
    {
        $encoded = json_encode($data);

        $this->handleDecodeError();

        return $encoded;
    }

    /**
     * @throws CodecException
     */
    private function handleDecodeError()
    {
        $message = 'Unknown error';

        switch(json_last_error()) {
            case JSON_ERROR_NONE:
                return;
                break;
        }

        throw new CodecException($message);
    }
}
