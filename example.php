<?php

require 'vendor/autoload.php';

use JsonRpcServer\Server;

$server = Server::createDefault();

$server->addMethod('hello', '\\JsonRpcServerTest\\Mocks\\TestService');
$server->addMethod('hello.person', '\\JsonRpcServerTest\\Mocks\\TestService', 'helloPerson');
$server->addMethod('namedParams', '\\JsonRpcServerTest\\Mocks\\TestService');

$server->handle()->output();
