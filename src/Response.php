<?php

namespace JsonRpcServer;

/**
 * Class Response
 * @package JsonRpcServer
 */
class Response
{
    /**
     * @var string
     */
	private $body;

    /**
     * @param $body
     * @return $this
     */
	public function setBody($body)
	{
		$this->body = $body;

		return $this;
	}

    /**
     * @return string
     */
	public function getBody()
	{
		return $this->body;
	}

    /**
     * Outputs the request content
     */
	public function output()
	{
		echo $this->body;
	}
}