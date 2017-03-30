# JSON-RPC Server

Welcome to JSON-RPC Server library written in PHP and fully supporting the
JSON-RPC 2.0 specification. It can work with incoming connections over HTTP using POST method but 
can be easily extended to read from any input.

Current version: **0.9.0** 

Release date: **2017-03-30**

License: **MIT**

## Installation

To start using in your project, run composer command:
```
composer require barell/json-rpc-server
```

## Basic Usage

There is a simplest way of using the server:

```php
class ExampleService
{
    public function hello($name)
    {
        return 'Hello ' . $name . '!';
    }
}

use JsonRpcServer\Server;

// Create server instance with default options
$server = Server::createDefault();

// Add hello method from ExampleService class
$server->addMethod('hello', '\ExampleService');

// Finally handle and output the result
$server->handle()->output();
```
The method hello is now externally available to call over HTTP POST using request like:
```json
{
  "jsonrpc": "2.0",
  "method": "hello",
  "params": {
    "name": "John"
  },
  "id": 1
}
```
And the output should be:
```json
{
  "jsonrpc": "2.0",
  "result": "Hello John!",
  "id": 1
}
```
