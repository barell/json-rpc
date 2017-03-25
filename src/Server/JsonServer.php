<?php

namespace PhpRpc\Server;

use JsonRpcServer\AbstractServer;
use JsonRpcServer\Codec\JsonCodec;
use JsonRpcServer\Exception\CodecException;
use JsonRpcServer\Exception\InvalidRequestException;

/**
 * Class JsonServer
 * @package PhpRpc\Server
 */
class JsonServer extends AbstractServer
{
    const SERVER_VERSION = '2.0';

    const ERROR_PARSE = -32700;
    const ERROR_INTERNAL = -32603

    /**
     * JsonServer constructor.
     */
    public function __construct()
    {
        $codec = new JsonCodec();
        $this->setCodec($codec);
    }

    public function handle()
    {
        try {
            // maybe move handler and codec calls to abstract server and make handleInternal as abstract instead of overwrite
            $data = $this->getHandler()->getData();
            $request = $this->getCodec()->decode($data);
        } catch (CodecException $e) {
            return $this->generateError(self::ERROR_PARSE);
        } catch (\Exception $e) {
            return $this->generateError(, 'Internal Error');
        }

        //foreach ($request->getCalls() as $call) {
            //$response->add? $this->execute($call);
        //}
//    } catch (InvalidRequestException $e) {
//return $this->generateError(-32600, 'Invalid Request');
        //return $response;
    }

    protected function execute($call)
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

    private function generateError($code, $message, $data = null)
    {
        $content = [
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];

        if ($data !== null) {
            $content['error']['data'] = $data;
        }

        return $this->generateReply($content);
    }

    private function generateResult($result)
    {
        $content = [
            'result' => $result
        ];

        return $this->generateReply($content);
    }

    private function generateReply($reply, $id = null)
    {
        $reply['jsonrpc'] = self::SERVER_VERSION;
        $reply['id'] = $id;

        return $reply;
    }
}
