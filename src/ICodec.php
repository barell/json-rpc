<?php

namespace JsonRpcServer;

/**
 * Interface ICodec
 * @package JsonRpcServer
 */
interface ICodec
{
    /**
     * @param $data
     * @return mixed
     */
    public function decode($data);

    /**
     * @param $data
     * @return mixed
     */
    public function encode($data);
}
