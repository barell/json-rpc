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
     * Outputs the request content
     */
	public function output()
	{
	    header('Content-Type: application/json');

		echo $this->content;
	}
}