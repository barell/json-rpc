<?php

require 'vendor/autoload.php';

use JsonRpcServer\Server;

$server = Server::createDefault();

$server->addMethod('hello', 'JsonRpcServerTest\\Mocks\\TestService');
$server->addMethod('hello.second', 'JsonRpcServerTest\\Mocks\\TestService', 'hello');
$server->handle()->output();
