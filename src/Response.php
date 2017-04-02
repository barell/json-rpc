<?php

namespace JsonRpcServer;

/**
 * Class Response
 * @package JsonRpcServer
 */
class Response
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var bool
     */
    private $headersSent = false;

    /**
     * Response constructor.
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->setContent($content);
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $name
     * @param null $value
     * @return $this
     */
    public function addHeader($name, $value = null)
    {
        if ($value === null) {
            $this->headers[] = $name;
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return $this
     */
    public function sendHeaders()
    {
        if (!$this->headersSent) {
            foreach ($this->headers as $key => $value) {
                if (is_string($key)) {
                    header($key . ': ' . $value);
                } else {
                    header($value);
                }
            }

            $this->headersSent = true;
        }

        return $this;
    }

    /**
     * Outputs the request content
     */
    public function output()
    {
        $this->sendHeaders();

        echo $this->content;
    }
}
