<?php

namespace JsonRpcServer;

/**
 * Class AbstractServer
 * @package JsonRpcServer
 */
abstract class AbstractServer
{
    /**
     * @var IHandler
     */
	private $handler;

    /**
     * @var ICodec
     */
	private $codec;

    /**
     * @var array
     */
	private $methods;

    /**
     * @param ICodec $codec
     * @return $this
     */
	public function setCodec(ICodec $codec)
	{
		$this->codec = $codec;

		return $this;
	}

    /**
     * @return ICodec
     */
	public function getCodec()
	{
		return $this->codec;
	}

    /**
     * @param IHandler $handler
     * @return $this
     */
	public function setHandler(IHandler $handler)
	{
		$this->handler = $handler;

		return $this;
	}

    /**
     * @return IHandler
     */
	public function getHandler()
	{
		return $this->handler;
	}

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
	public function addMethod($name, $callback)
	{
		$this->methods[$name] = $callback;

		return $this;
	}

    /**
     * @param $name
     * @return bool
     */
	public function hasMethod($name) 
	{
		return array_key_exists($name, $this->methods);
	}

    /**
     * @param $name
     * @return mixed
     */
	public function getMethod($name) 
	{
		return $this->methods[$name];
	}

    /**
     * @return array
     */
	public function getMethods()
	{
		return $this->methods;
	}

    /**
     * @return Response
     */
	abstract public function handle();

    /**
     * @param $call
     * @return mixed
     */
	abstract protected function execute($call);
}
