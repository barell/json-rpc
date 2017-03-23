<?php

namespace PhpRpc\Server;

class Response
{
	private $body;

	public function setBody($body)
	{
		$this->body = $body;

		return $this;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function output()
	{
		echo $this->body;
	}
}