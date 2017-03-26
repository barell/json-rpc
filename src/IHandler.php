<?php

namespace JsonRpcServer;

/**
 * Interface IHandler
 * @package JsonRpcServer
 */
interface IHandler
{
    /**
     * @return mixed
     */
    public function getData();
}
