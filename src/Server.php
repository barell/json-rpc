<?php

namespace PhpRpc;

use \PhpRpc\Server\IHandler;
use \PhpRpc\Server\ICodec;
use \PhpRpc\Server\Response;
use \PhpRpc\Server\Exception\CodecException;

class Server
{
	private $handler;
	private $codec;
	private $methods;

	public function setCodec(ICodec $codec)
	{
		$this->codec = $codec;

		return $this;
	}

	public function getCodec()
	{
		return $this->codec;
	}

	public function setHandler(IHandler $handler)
	{
		$this->handler = $handler;

		return $this;
	}

	public function getHandler()
	{
		return $this->handler;
	}

	public function addMethod($name, $callback)
	{
		$this->methods[$name] = $callback;

		return $this;
	}

	public function hasMethod($name) 
	{
		return array_key_exists($name, $this->methods);
	}

	public function getMethod($name) 
	{
		return $this->methods[$name];
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function handle()
	{
		$response = new Response();

		try {
			$data = $this->handler->getData();
			$request = $this->codec->decode($data);
		} catch (CodecException $e) {
			var_dump($e->getMessage());
			exit;
			// generate RPC error using the codec: Parse error -32700
			//$response = codec_generate_error(Methodnotfound)
		} catch (InvalidRequestException $e) {
			// codec need to check if there is a method, parameters (jsonrpc:2.0, etc); Invalid Request -32600
			//$response = codec_generate_error(...)
		} catch (Exception $e) {
			// Internal error? (-32603)
			//$response = codec_generate_error(...)
		}

		foreach ($request->getCalls() as $call) {
			//$response->add? $this->execute($call);
		}

		//return $response;
	}

	private function execute($call) 
	{
		// check if method name exists
		//if (!$this->hasMethod($call->getMethod()) {
			// return method not found error Method not found -32601
		//}
		// check if parameters match
		// if not return Invalid params -32602	

		try {
			$someData = call_user_func($callback);
		} catch (RpcUserError $e) {
			// generate error response using code and message from the exception
		} catch (Exception $e) {
			// anything else put it here as internal server error (-32603)
		}
	}
}
