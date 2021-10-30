<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetHeader()
    {
        $response = new Response(200, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals('15', $response->getHeader('Content-Length'));
        $this->assertNull($response->getHeader('Not-Exists'));
    }

    public function testGetHeaders()
    {
        $response = new Response(200, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $headers = $response->getHeaders();
        $this->assertEquals(2, count($headers));
        $this->assertEquals('application/json', $headers['Content-Type']);
        $this->assertEquals('15', $headers['Content-Length']);
    }

    public function testIsSucceeded()
    {
        $response = new Response(200, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals(true, $response->isSucceeded());

        $response = new Response(202, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals(true, $response->isSucceeded());

        $response = new Response(299, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals(true, $response->isSucceeded());

        $response = new Response(199, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals(false, $response->isSucceeded());

        $response = new Response(300, '{"body":"text"}', [
            'Content-Type' => 'application/json',
            'Content-Length' => '15',
        ]);
        $this->assertEquals(false, $response->isSucceeded());
    }
}
