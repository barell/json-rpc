<?php

namespace JsonRpcServer\Response;

/**
 * Class Builder
 * @package JsonRpcServer\Response
 */
class Builder
{
    const SINGLE_BUILD = 'single';
    const MULTI_BUILD = 'multi';

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

    /**
     * @param $build
     * @return array
     */
    public function getRepliesCombined($build)
    {
        if (!count($this->replies)) {
            return [];
        }

        if ($build == self::SINGLE_BUILD) {
            return $this->replies[0];
        }

        return $this->replies;
    }
}
