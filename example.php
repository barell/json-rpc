<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

class ExampleService
{
	public function hello()
	{
		return 'Hello world!';
	}
}

$handler = new HttpPostHandler();
$codec = new JsonCodec();

$server = new Server();

$server->setHandler($handler);
$server->setCodec($codec);

$server->addMethod('hello', array('ExampleService', 'hello'));

$server->handle()->output();

// or return details about all handler callbacks
$server->reflector()->getDetails();
