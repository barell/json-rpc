<?php

namespace JsonRpcServer;

use JsonRpcServer\Request\Validator;

/**
 * Class Request
 * @package JsonRpcServer
 */
class Request
{
    /**
     * @var array
     */
    private $calls;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Request constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->validator = new Validator();

        $this->validator->validateInput($data);
        $this->process($data);
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @param array $data
     */
    private function process(array $data)
    {
        if (array_key_exists(0, $data)) {
            $this->calls = $data;
        } else {
            $this->calls = [
                $data
            ];
        }
    }
}
