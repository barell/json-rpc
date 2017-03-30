<?php

namespace JsonRpcServer\Request;

use JsonRpcServer\Exception\InvalidRequestException;
use JsonRpcServer\Server;

/**
 * Class Validator
 * @package JsonRpcServer\Request
 */
class Validator
{
    /**
     * @param $input
     * @throws InvalidRequestException
     */
    public function validateInput($input)
    {
        if (!is_array($input)) {
            throw new InvalidRequestException('The request input must be an array');
        }
    }

    /**
     * @param $call
     * @throws InvalidRequestException
     */
    public function validateCall($call)
    {
        if (!is_array($call)) {
            throw new InvalidRequestException('A single call request must be an array');
        }

        if (!array_key_exists('jsonrpc', $call) || $call['jsonrpc'] !== Server::JSON_RPC_VERSION) {
            throw new InvalidRequestException('JSON-RPC version key is missing or does not match the server');
        }

        if (!array_key_exists('method', $call)) {
            throw new InvalidRequestException('Method name is missing');
        }

        if (array_key_exists('id', $call) && !is_int($call['id']) && !is_string($call['id']) && !is_null($call['id'])) {
            throw new InvalidRequestException('Id type is not valid');
        }
    }
}
