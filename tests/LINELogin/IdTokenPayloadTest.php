<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\IdTokenPayload;
use EJLin\LINELogin\UserProfile;
use PHPUnit\Framework\TestCase;

class IdTokenPayloadTest extends TestCase
{
    public function testParseMapping()
    {
        $idTokenPayload = IdTokenPayload::parseToIdTokenPayloadObject([
            'iss' => 'https://access.line.me',
            'sub' => 'U1234567890abcdef1234567890abcdef',
            'aud' => '1234567890',
            'exp' => 1504169092,
            'iat' => 1504263657,
            'auth_time' => 1504263622,
            'nonce' => '0987654asdf',
            'amr' => [
                'pwd',
                'linesso',
                'lineqr',
            ],
            'name' => 'Taro Line',
            'picture' => 'https://sample_line.me/aBcdefg123456',
            'email' => 'taro.line@example.com',
        ]);

        $this->assertEquals('https://access.line.me', $idTokenPayload->iss);
        $this->assertEquals('U1234567890abcdef1234567890abcdef', $idTokenPayload->getUserId());
        $this->assertEquals('1234567890', $idTokenPayload->aud);
        $this->assertEquals(1504169092, $idTokenPayload->exp);
        $this->assertEquals(1504263657, $idTokenPayload->iat);
        $this->assertEquals(1504263622, $idTokenPayload->authTime);
        $this->assertEquals('0987654asdf', $idTokenPayload->getNonce());
        $this->assertEquals([
            'pwd',
            'linesso',
            'lineqr',
        ], $idTokenPayload->amr);
        $this->assertEquals('Taro Line', $idTokenPayload->getUserDisplayName());
        $this->assertEquals('https://sample_line.me/aBcdefg123456', $idTokenPayload->getUserPictureUrl());
        $this->assertEquals('taro.line@example.com', $idTokenPayload->getUserEmail());
    }

    public function testParseMappingWithoutOptionalValue()
    {
        $idTokenPayload = IdTokenPayload::parseToIdTokenPayloadObject([
            'iss' => 'https://access.line.me',
            'sub' => 'U1234567890abcdef1234567890abcdef',
            'aud' => '1234567890',
            'exp' => 1504169092,
            'iat' => 1504263657,
            'amr' => [
                'pwd',
                'linesso',
                'lineqr',
            ],
        ]);

        $this->assertEquals('https://access.line.me', $idTokenPayload->iss);
        $this->assertEquals('U1234567890abcdef1234567890abcdef', $idTokenPayload->sub);
        $this->assertEquals('1234567890', $idTokenPayload->aud);
        $this->assertEquals(1504169092, $idTokenPayload->exp);
        $this->assertEquals(1504263657, $idTokenPayload->iat);
        $this->assertEquals([
            'pwd',
            'linesso',
            'lineqr',
        ], $idTokenPayload->amr);
        $this->assertEquals(null, $idTokenPayload->authTime);
        $this->assertEquals(null, $idTokenPayload->nonce);
        $this->assertEquals(null, $idTokenPayload->name);
        $this->assertEquals(null, $idTokenPayload->picture);
        $this->assertEquals(null, $idTokenPayload->email);
    }

    public function testToUserProfile()
    {
        $idTokenPayload = IdTokenPayload::parseToIdTokenPayloadObject([
            'iss' => 'https://access.line.me',
            'sub' => 'U1234567890abcdef1234567890abcdef',
            'aud' => '1234567890',
            'exp' => 1504169092,
            'iat' => 1504263657,
            'auth_time' => 1504263622,
            'nonce' => '0987654asdf',
            'amr' => [
                'pwd',
                'linesso',
                'lineqr',
            ],
            'name' => 'Taro Line',
            'picture' => 'https://sample_line.me/aBcdefg123456',
            'email' => 'taro.line@example.com',
        ]);

        $userProfile = $idTokenPayload->toUserProfile();

        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertEquals('U1234567890abcdef1234567890abcdef', $userProfile->getUserId());
        $this->assertEquals('Taro Line', $userProfile->getDisplayName());
        $this->assertEquals('https://sample_line.me/aBcdefg123456', $userProfile->getPictureUrl());
        $this->assertEquals('taro.line@example.com', $userProfile->getEmail());
        $this->assertEquals(null, $userProfile->getStatusMessage());
    }
}