<?php

namespace JsonRpcServer;

use JsonRpcServer\Codec\JsonCodec;
use JsonRpcServer\Exception\CodecException;
use JsonRpcServer\Exception\InvalidRequestException;
use JsonRpcServer\Exception\JsonRpcUserException;
use JsonRpcServer\Handler\HttpHandler;
use JsonRpcServer\Server\Call;
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
    const ERROR_METHOD_NOT_FOUND = -32601;
    const ERROR_INVALID_PARAMS = -32602;
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
     * @param $class
     * @param null $method
     * @return $this
     */
    public function addMethod($name, $class, $method = null)
    {
        if ($method === null) {
            $method = $name;
        }

        $this->methods[$name] = [$class, $method];

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
            $reply = $this->execute($call, $request);

            if ($reply !== null) {
                $reply = $this->buildReply($reply);
                $responseBuilder->addReply($reply);
            }
        }

        $build = Builder::SINGLE_BUILD;
        if ($request->getTotalCalls() > 1) {
            $build = Builder::MULTI_BUILD;
        }

        $result = $responseBuilder->getRepliesCombined($build);
        $encoded = '';

        if (!empty($result)) {
            $encoded = $this->getCodec()->encode($result);
        }

        return new Response($encoded);
    }

    /**
     * @param $callData
     * @param Request $request
     * @return array|null
     */
    private function execute($callData, Request $request)
    {
        $callId = null;
        if (is_array($callData) && array_key_exists('id', $callData)) {
            $callId = $callData['id'];
        }

        try {
            $request->getValidator()->validateCall($callData);
        } catch (InvalidRequestException $e) {
            return $this->buildErrorReply(self::ERROR_INVALID_REQUEST, $callId);
        }

        $call = new Call($callData);
        $method = $call->getMethod();
        $params = $call->getParams();

        if (!$this->hasMethod($method) || !is_callable($this->getMethod($method))) {
            return $this->buildErrorReply(self::ERROR_METHOD_NOT_FOUND, $callId);
        }

        $callback = $this->getMethod($method);
        $reflector = new \ReflectionMethod($callback[0], $callback[1]);

        if ($call->hasNamedParams()) {
            foreach ($reflector->getParameters() as $parameter) {
                if (!$parameter->isOptional() && !array_key_exists($parameter->getName(), $params)) {
                    return $this->buildErrorReply(
                        self::ERROR_INVALID_PARAMS,
                        $callId,
                        sprintf('Parameter %s is required', $parameter->getName())
                    );
                }
            }
        } else {
            if ($reflector->getNumberOfRequiredParameters() > count($params)) {
                return $this->buildErrorReply(
                    self::ERROR_INVALID_PARAMS,
                    $callId,
                    'Missing parameters'
                );
            }
        }

        try {
            $result = call_user_func_array($callback, $params);
        } catch (JsonRpcUserException $e) {
            return $this->buildErrorReply($e->getCode(), $callId, $e->getMessage());
        } catch (\Exception $e) {
            return $this->buildErrorReply(self::ERROR_INTERNAL, $callId);
        }

        if ($call->getType() == Call::TYPE_NOTIFICATION) {
            return null;
        }

        return $this->buildResultReply($result, $callId);
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
     * @return array
     */
    private function buildResultReply($result, $id = null)
    {
        $reply = [
            'result' => $result,
            'id' => $id
        ];

        return $reply;
    }

    /**
     * @param $code
     * @param null $id
     * @param null $message
     * @param null $data
     * @return array
     */
    private function buildErrorReply($code, $id = null, $message = null, $data = null)
    {
        if ($message === null) {
            $message = $this->getErrorMessage($code);
        }

        $reply = [
            'code' => $code,
            'message' => $message
        ];

        if ($data !== null) {
            $reply['data'] = $data;
        }

        $reply['id'] = $id;

        return $reply;
    }

    /**
     * @param $reply
     * @param null $id
     * @return array
     */
    private function buildReply($reply, $id = null)
    {
        if (!array_key_exists('jsonrpc', $reply)) {
            $reply = ['jsonrpc' => self::JSON_RPC_VERSION] + $reply;
        }

        if (!array_key_exists('id', $reply)) {
            $reply['id'] = $id;
        }

        return $reply;
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
        $errorReply = $this->buildErrorReply($code, $id, $message, $data);
        $reply = $this->buildReply($errorReply, $id);
        $encoded = $this->getCodec()->encode($reply);

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
                return 'Parse error';
            case self::ERROR_INVALID_REQUEST:
                return 'Invalid request';
            case self::ERROR_METHOD_NOT_FOUND:
                return 'Method not found';
            case self::ERROR_INVALID_PARAMS:
                return 'Invalid params';
            case self::ERROR_INTERNAL:
                return 'Internal error';
        }

        return 'Unknown error';
    }
}
