<?php

namespace JsonRpcServer\Server;

/**
 * Class Call
 * @package JsonRpcServer\Server
 */
class Call
{
    const TYPE_NOTIFICATION = 'notification';
    const TYPE_NORMAL = 'normal';

    /**
     * @var mixed
     */
    private $method;

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $type;

    /**
     * Call constructor.
     * @param array $call
     */
    public function __construct(array $call)
    {
        $this->method = $call['method'];
        $this->id = null;
        $this->type = self::TYPE_NOTIFICATION;
        $this->params = [];

        if (array_key_exists('id', $call)) {
            $this->id = $call['id'];
            $this->type = self::TYPE_NORMAL;
        }

        if (array_key_exists('params', $call)) {
            $this->params = $call['params'];
        }
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array|mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hasNamedParams()
    {
        if (!count($this->params)) {
            return true;
        }

        foreach ($this->params as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }
}
