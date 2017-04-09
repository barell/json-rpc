[![Build Status](https://travis-ci.org/barell/json-rpc-server.svg?branch=master)](https://travis-ci.org/barell/json-rpc-server)

# JSON-RPC Server

Welcome to JSON-RPC Server library written in PHP 5.5 and higher and fully supporting the
[JSON-RPC 2.0 specification](http://www.jsonrpc.org/specification). It can work with incoming connections over HTTP using POST method but 
can be easily extended to read from any input.

Current version: **1.0.2** 

Release date: **2017-04-09**

License: **MIT**

## Installation

To start using in your project, run composer command:
```
composer require barell/json-rpc-server
```

## Basic Usage

Below the example of how to expose a *hello* method of the class *ExampleService*:

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
$server->addMethod('hello', 'ExampleService');

// Finally handle and output the result
$server->handle()->output();
```
The method *hello* is now externally available to call over HTTP POST using request like:
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
The output will be:
```json
{
  "jsonrpc": "2.0",
  "result": "Hello John!",
  "id": 1
}
```

### Custom Errors

By default any exceptions returned within your exposed method will be shown as internal server error.
To return customer error message and code, please throw an exception using *JsonRpcUserException* class any derivative of it. 
See example:

```php
class ExampleService
{
    public function divide($a, $b)
    {
        if ($b == 0) {
            throw new JsonRpcUserException('Division by zero is not allowed', 1234);
        }
        
        return $a / $b;
    }
}
```
Whenever zero will be passed as the second parameter, the server output will be:
```json
{
  "jsonrpc": "2.0",
  "code": 1234,
  "message": "Division by zero is not allowed",
  "id": null
}
```
