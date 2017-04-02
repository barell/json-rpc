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
        return json_encode($data);
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
            case JSON_ERROR_DEPTH:
                $message = 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_RECURSION:
                $message = 'One or more recursive references in the value to be encoded';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $message = 'One or more NAN or INF values in the value to be encoded';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $message = 'A value of a type that cannot be encoded was given';
                break;
        }

        throw new CodecException($message);
    }
}
