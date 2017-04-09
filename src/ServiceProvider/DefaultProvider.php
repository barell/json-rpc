<?php

namespace JsonRpcServer\ServiceProvider;

use JsonRpcServer\IServiceProvider;

/**
 * Class DefaultProvider
 * @package JsonRpcServer\ServiceProvider
 */
class DefaultProvider implements IServiceProvider
{
    /**
     * @param $service
     * @return mixed
     */
    public function getServiceObject($service)
    {
        if (!is_object($service)) {
            $service = new $service;
        }

        return $service;
    }
}
