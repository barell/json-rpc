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
        $server->addMethod('something', 'JsonRpcServerTest\\Mocks\\TestService');
        $server->addMethod('notify', 'JsonRpcServerTest\\Mocks\\TestService');

        return $server;
    }

    public function testHasMethods()
    {
        $server = $this->getServer('');

        $this->assertEquals(5, count($server->getMethods()));

        $this->assertEquals(true, $server->hasMethod('hello'));
        $this->assertEquals(true, $server->hasMethod('hello.second'));
        $this->assertEquals(true, $server->hasMethod('helloPerson'));
        $this->assertEquals(true, $server->hasMethod('something'));
        $this->assertEquals(true, $server->hasMethod('notify'));

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
        $data = '{"jsonrpc":"2.0","method":"nonexistingmethod","id":null}';
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

    public function testSubName()
    {
        $data = '{"jsonrpc":"2.0","method":"hello.second","id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","result":"Hello World","id":null}', $response);
    }

    public function testParameterValues()
    {
        $data = '{"jsonrpc":"2.0","method":"something","params":{"a":2,"b":4},"id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","result":[2,4],"id":null}', $response);
    }

    public function testParameterAllValues()
    {
        $data = '{"jsonrpc":"2.0","method":"something","params":{"a":2,"b":4,"c":"hello"},"id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","result":[2,4,"hello"],"id":null}', $response);
    }

    public function testNotification()
    {
        $data = '{"jsonrpc":"2.0","method":"notify","params":{"arg":"test"}}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('', $response);
    }

    public function testId()
    {
        $data = '{"jsonrpc":"2.0","method":"hello","id":123}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","result":"Hello World","id":123}', $response);
    }

    public function testBatchEmpty()
    {
        $data = '[]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null}', $response);
    }

    public function testBatch()
    {
        $data = '[{"jsonrpc":"2.0","method":"hello","id":123},{"jsonrpc":"2.0","method":"helloPerson","params":{"name":"John"},"id":999}]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('[{"jsonrpc":"2.0","result":"Hello World","id":123},{"jsonrpc":"2.0","result":"Hello John!","id":999}]', $response);
    }

    public function testBatchWithNotification()
    {
        $data = '[{"jsonrpc":"2.0","method":"hello","id":123},{"jsonrpc":"2.0","method":"notify","params":{"arg":"Test"}}]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('[{"jsonrpc":"2.0","result":"Hello World","id":123}]', $response);
    }

    public function testBatchWithWithInvalid()
    {
        $data = '[{"jsonrpc":"2.0","method":"hello","id":123},{"test":"abc"}]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('[{"jsonrpc":"2.0","result":"Hello World","id":123},{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null}]', $response);
    }

    public function testBatchWithWithMethodNotFound()
    {
        $data = '[{"jsonrpc":"2.0","method":"hello","id":123},{"jsonrpc":"2.0","method":"abcd","id":null}]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('[{"jsonrpc":"2.0","result":"Hello World","id":123},{"jsonrpc":"2.0","code":-32601,"message":"Method not found","id":null}]', $response);
    }

    public function testBatchAllNotifications()
    {
        $data = '[{"jsonrpc":"2.0","method":"hello"},{"jsonrpc":"2.0","method":"hello.second"}]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('', $response);
    }

    public function testBatchAllInvalid()
    {
        $data = '[1,2]';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('[{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null},{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null}]', $response);
    }

    public function testWrongSpecVersion()
    {
        $data = '{"jsonrpc":"3.0","method":"notify","params":{"arg":"test"},"id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null}', $response);
    }

    public function testMissingMethodName()
    {
        $data = '{"jsonrpc":"2.0","params":{"arg":"test"},"id":null}';
        $server = $this->getServer($data);
        $response = $server->handle()->getContent();

        $this->assertEquals('{"jsonrpc":"2.0","code":-32600,"message":"Invalid request","id":null}', $response);
    }
}
