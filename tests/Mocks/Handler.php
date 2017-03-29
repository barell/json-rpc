<?php

namespace JsonRpcServerTest\Mocks;

use JsonRpcServer\IHandler;

class Handler implements IHandler
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
