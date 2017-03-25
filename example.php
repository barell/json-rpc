<?php

spl_autoload_register(function ($className) {

	$parts = explode('\\', $className);

	if ($parts[0] == 'PhpRpc') {
		$parts[0] = 'src';
    	$file = implode('/', $parts) . '.php';
    	
    	if (file_exists($file)) {
    		require $file;
    	}
    }
});

class ExampleService
{
	public function hello()
	{
		return 'Hello world!';
	}
}

use PhpRpc\Server\Handler\HttpHandler;
use PhpRpc\Server\Codec\JsonCodec;
use PhpRpc\AbstractServer;

$handler = new HttpHandler();
$codec = new JsonCodec();

$server = new AbstractServer();

$server->setHandler($handler);
$server->setCodec($codec);

$server->addMethod('hello', array('ExampleService', 'hello'));

$server->handle()->output();

// or return details about all handler callbacks
//$server->reflector()->getDetails();
