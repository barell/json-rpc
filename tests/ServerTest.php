<?php

namespace JsonRpcServerTest;

use JsonRpcServer\Codec\JsonCodec;
use JsonRpcServer\Server;
use JsonRpcServerTest\Mocks\Handler;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    private function getServer($data)
    {
        $codec = new JsonCodec();
        $handler = new Handler($data);

        $server = new Server();
        $server->setCodec($codec);
        $server->setHandler($handler);

        $server->addMethod('hello', 'JsonRpcServerTest\\Mocks\\TestService');
        $server->addMethod('hello.second', 'JsonRpcServerTest\\Mocks\\TestService', 'hello');
        $server->addMethod('helloPerson', 'JsonRpcServerTest\\Mocks\\TestService');

        return $server;
    }

    public function testHasMethods()
    {
        $server = $this->getServer('');

        $this->assertEquals(3, count($server->getMethods()));

        $this->assertEquals(true, $server->hasMethod('hello'));
        $this->assertEquals(true, $server->hasMethod('hello.second'));
        $this->assertEquals(true, $server->hasMethod('helloPerson'));

        $this->assertEquals(false, $server->hasMethod('abcd'));
    }

    public function testInvalidInput()
    {
        $data = 'badJson';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32700,"message":"Parse error","id":null}', $response);
    }

    public function testMethodNotFound()
    {
        $data = '{"jsonrpc":"2.0","method":"something","id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32601,"message":"Method not found","id":null}', $response);
    }

    public function testMissingParameter()
    {
        $data = '{"jsonrpc":"2.0","method":"helloPerson","id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32602,"message":"Parameter name is required","id":null}', $response);
    }
}
