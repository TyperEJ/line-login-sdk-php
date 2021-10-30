<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testParseMapping()
    {
        $token = Token::parseToTokenObject([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => '2592000',
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $this->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $token->getAccessToken());
        $this->assertEquals('2592000', $token->getExpiredIn());
        $this->assertEquals('eyJhbGciOiJIUzI1NiJ9', $token->getIdToken());
        $this->assertEquals('Aa1FdeggRhTnPNNpxr8p', $token->getRefreshToken());
        $this->assertEquals('profile', $token->getScope());
        $this->assertEquals('Bearer', $token->getTokenType());
    }

    public function testParseMappingWithoutIdToken()
    {
        $token = Token::parseToTokenObject([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => '2592000',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $this->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $token->getAccessToken());
        $this->assertEquals('2592000', $token->getExpiredIn());
        $this->assertEquals(null, $token->getIdToken());
        $this->assertEquals('Aa1FdeggRhTnPNNpxr8p', $token->getRefreshToken());
        $this->assertEquals('profile', $token->getScope());
        $this->assertEquals('Bearer', $token->getTokenType());
    }
}