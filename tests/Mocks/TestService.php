<?php

namespace JsonRpcServerTest\Mocks;

class TestService
{
    public function hello()
    {
        return 'Hello World';
    }

    public function helloPerson($name)
    {
        return 'Hello ' . $name . '!';
    }

    public function something($a, $b, $c = 'test')
    {
        return func_get_args();
    }

    public function notify($arg)
    {

    }
}