# JSON-RPC Server

Welcome to JSON-RPC Server library written in PHP and fully supporting the
JSON-RPC 2.0 specification. It can work with incoming connections over HTTP using POST method but 
can be easily extended to read from any input.

Current version: **No release yet** 

Release date: **No release yet**

License: **MIT**

## Installation

Coming soon

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
