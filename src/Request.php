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

    /**<?php

spl_autoload_register(function ($className) {
	$parts = explode('\\', $className);
	if ($parts[0] == 'JsonRpcServer') {
		$parts[0] = 'src';
    	$file = implode('/', $parts) . '.php';

    	if (file_exists($file)) {
    		require $file;
    	}
    }
});

class ExampleService
{
	public function hello($name)
	{
		return 'Hello ' . $name . '!';
	}
}

use JsonRpcServer\Server;

$server = Server::createDefault();

$server->addMethod('hello', '\ExampleService');
$server->handle()->output();

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
