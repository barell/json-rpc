<?php

namespace JsonRpcServer\Response;

/**
 * Class Builder
 * @package JsonRpcServer\Response
 */
class Builder
{
    /**
     * @var array
     */
    private $replies = [];

    /**
     * @param $reply
     */
    public function addReply($reply)
    {
        $this->replies[] = $reply;
    }

    public function getRepliesCombined()
    {

    }
}
