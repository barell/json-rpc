<?php

namespace JsonRpcServer;

use JsonRpcServer\Codec\JsonCodec;
use JsonRpcServer\Exception\CodecException;
use JsonRpcServer\Exception\InvalidRequestException;
use JsonRpcServer\Exception\JsonRpcServerException;
use JsonRpcServer\Handler\HttpHandler;
use JsonRpcServer\Response\Builder;

/**
 * Class Server
 * @package JsonRpcServer
 */
class Server
{
    /**
     * Creates a new instance of the server using default JSON codec and HTTP POST handler.
     *
     * @return Server
     */
    public static function createDefault()
    {
        $codec = new JsonCodec();
        $handler = new HttpHandler();

        $server = new self();
        $server->setCodec($codec);
        $server->setHandler($handler);

        return $server;
    }

    const JSON_RPC_VERSION = '2.0';

    const ERROR_PARSE = -32700;
    const ERROR_INVALID_REQUEST = -32600;
    const ERROR_INTERNAL = -32603;

    /**
     * @var IHandler
     */
	private $handler;

    /**
     * @var ICodec
     */
	private $codec;

    /**
     * @var array
     */
	private $methods;

    /**
     * @param ICodec $codec
     * @return $this
     */
	public function setCodec(ICodec $codec)
	{
		$this->codec = $codec;

		return $this;
	}

    /**
     * @return ICodec
     */
	public function getCodec()
	{
		return $this->codec;
	}

    /**
     * @param IHandler $handler
     * @return $this
     */
	public function setHandler(IHandler $handler)
	{
		$this->handler = $handler;

		return $this;
	}

    /**
     * @return IHandler
     */
	public function getHandler()
	{
		return $this->handler;
	}

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
	public function addMethod($name, $callback)
	{
		$this->methods[$name] = $callback;

		return $this;
	}

    /**
     * @param $name
     * @return bool
     */
	public function hasMethod($name) 
	{
		return array_key_exists($name, $this->methods);
	}

    /**
     * @param $name
     * @return mixed
     */
	public function getMethod($name) 
	{
		return $this->methods[$name];
	}

    /**
     * @return array
     */
	public function getMethods()
	{
		return $this->methods;
	}

    /**
     * @return Response
     */
	public function handle()
    {
        try {
            $request = $this->buildRequest();
        } catch (CodecException $e) {
            return $this->getErrorResponse(self::ERROR_PARSE);
        } catch (InvalidRequestException $e) {
            return $this->getErrorResponse(self::ERROR_INVALID_REQUEST);
        } catch (\Exception $e) {
            return $this->getErrorResponse(self::ERROR_INTERNAL);
        }

        $responseBuilder = new Builder();
        foreach ($request->getCalls() as $call) {
            $reply = $this->execute($call);

            if ($reply !== null) {
                $responseBuilder->addReply($reply);
            }
        }

        $result = $responseBuilder->getRepliesCombined();
        $encoded = $this->getCodec()->encode($result);

        return new Response($encoded);
    }

    private function execute($call)
    {
        try {
            $someData = call_user_func($callback);
        } catch (RpcUserError $e) {
            // generate error response using code and message from the exception
        } catch (Exception $e) {
            // anything else put it here as internal server error (-32603)
        }

        return [];
    }

    /**
     * @return Request
     */
    private function buildRequest()
    {
        $input = $this->getHandler()->getData();
        $data = $this->getCodec()->decode($input);

        return new Request($data);
    }

    /**
     * @param $result
     * @param null $id
     * @return Response
     */
    private function getResultResponse($result, $id = null) {
        $data = [
            'result' => $result
        ];

        return $this->getResponse($data, $id);
    }

    /**
     * @param $code
     * @param null $message
     * @param null $data
     * @param null $id
     * @return Response
     */
    private function getErrorResponse($code, $message = null, $data = null, $id = null)
    {
        if ($message === null) {
            $message = $this->getErrorMessage($code);
        }

        $error = [
            'code' => $code,
            'message' => $message
        ];

        if ($data !== null) {
            $error['data'] = $data;
        }

        return $this->getResponse($error, $id);
    }

    /**
     * @param $data
     * @param null $id
     * @return Response
     */
    private function getResponse($data, $id = null)
    {
        if (!array_key_exists('jsonrpc', $data)) {
            $data = ['jsonrpc' => self::JSON_RPC_VERSION] + $data;
        }

        if (!array_key_exists('id', $data)) {
            $data['id'] = $id;
        }

        $encoded = $this->getCodec()->encode($data);

        return new Response($encoded);
    }

    /**
     * @param $code
     * @return string
     */
    private function getErrorMessage($code)
    {
        switch ($code) {
            case self::ERROR_PARSE:
                return 'Parse Error';
            case self::ERROR_INVALID_REQUEST:
                return 'Invalid Request';
            case self::ERROR_INTERNAL:
                return 'Internal Error';
        }

        return 'Unknown Error';
    }
}
