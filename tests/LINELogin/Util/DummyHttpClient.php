<?php

namespace EJLin\Tests\LINELogin\Util;

use EJLin\LINELogin\HTTPClient;
use EJLin\LINELogin\Response;
use PHPUnit\Framework\TestCase;

class DummyHttpClient implements HTTPClient
{
    /** @var \PHPUnit\Framework\TestCase */
    private $testRunner;
    /** @var \Closure */
    private $mock;
    /** @var int */
    private $statusCode;

    public function __construct(TestCase $testRunner, \Closure $mock, $statusCode = 200)
    {
        $this->testRunner = $testRunner;
        $this->mock = $mock;
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $url
     * @param array $data Optional
     * @param array $headers
     * @return Response
     */
    public function get(string $url, array $data = [], array $headers = [])
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'GET', $url, is_null($data) ? [] : $data, $headers);
        return new Response($this->statusCode, json_encode($ret));
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $headers Optional
     * @return Response
     */
    public function post(string $url, array $data, array $headers = null)
    {
        $ret = call_user_func($this->mock, $this->testRunner, 'POST', $url, $data, $headers);
        return new Response($this->statusCode, json_encode($ret));
    }
}
