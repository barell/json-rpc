<?php

namespace JsonRpcServer;

/**
 * Interface IServiceProvider
 * @package JsonRpcServer
 */
interface IServiceProvider
{
    /**
     * @param $service
     * @return mixed
     */
    public function getServiceObject($service);
}
