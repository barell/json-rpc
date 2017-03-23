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

use PhpRpc\Server\Handler\Http;
use PhpRpc\Server\Codec\Json;
use PhpRpc\Server;

$handler = new Http();
$codec = new Json();

$server = new Server();

$server->setHandler($handler);
$server->setCodec($codec);

$server->addMethod('hello', array('ExampleService', 'hello'));

$server->handle()->output();

// or return details about all handler callbacks
//$server->reflector()->getDetails();
