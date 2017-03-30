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
     * @var int
     */
    private $totalCalls;

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
     * @return int
     */
    public function getTotalCalls()
    {
        return $this->totalCalls;
    }

    /**
     * @param array $data
     */
    private function process(array $data)
    {
        if (array_key_exists(0, $data)) {
            $this->calls = $data;
            $this->totalCalls = count($this->calls);

            return;
        }

        $this->totalCalls = 1;
        $this->calls = [
            $data
        ];

    }
}
