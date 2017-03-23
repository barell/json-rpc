<?php

namespace PhpRpc\Server\Handler;

use \PhpRpc\Server\IHandler;

class Http implements IHandler
{
	public function getData()
	{
		return file_get_contents('php://input');
	}
}
