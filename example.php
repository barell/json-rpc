<?php

require 'vendor/autoload.php';

use JsonRpcServer\Server;

$testService = new \JsonRpcServerTest\Mocks\TestService();

$server = Server::createDefault();

$server->addMethod('hello', $testService);
$server->addMethod('hello.person', $testService, 'helloPerson');

$server->handle()->output();
