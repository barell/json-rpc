<?php

namespace JsonRpcServer\Handler;

use JsonRpcServer\IHandler;

/**
 * Class HttpHandler
 * @package JsonRpcServer\Handler
 */
class HttpHandler implements IHandler
{
    /**
     * @return bool|string
     */
    public function getData()
    {
        return file_get_contents('php://input');
    }
}
