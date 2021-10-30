<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\Exception\AuthenticationException;
use EJLin\LINELogin\IdTokenPayload;
use EJLin\LINELogin;
use EJLin\LINELogin\Response;
use EJLin\LINELogin\Token;
use EJLin\LINELogin\UserProfile;
use EJLin\Tests\LINELogin\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;

class LINELoginTest extends TestCase
{
    public function testGenerateLoginUrl()
    {
        $lineLogin = new LINELogin(new DummyHttpClient($this, function () {
            return [];
        }), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $loginUrl = $lineLogin->makeAuthorizeUrl(
            'http://example.com',
            'profile openid email',
            'testnonce'
        );

        $expectedUrl = <<<URL
https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=1234567890&redirect_uri=http%3A%2F%2Fexample.com&scope=profile%20openid%20email&state=testnonce
URL;

        $this->assertEquals(
            $expectedUrl,
            $loginUrl
        );
    }

    public function testGenerateLoginUrlWithCodeChallenge()
    {
        $lineLogin = new LINELogin(new DummyHttpClient($this, function () {
            return [];
        }), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $loginUrl = $lineLogin->makePKCEAuthorizeUrl(
            'http://example.com',
            'profile openid email',
            'testnonce',
            '9ETE40L4xrAd6PbzWAtwz_Gkj-rNjEPZUwiBQ_u8e2I'
        );

        $expectedUrl = <<<URL
https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=1234567890&redirect_uri=http%3A%2F%2Fexample.com&scope=profile%20openid%20email&state=testnonce&code_challenge=9ETE40L4xrAd6PbzWAtwz_Gkj-rNjEPZUwiBQ_u8e2I&code_challenge_method=S256
URL;

        $this->assertEquals(
            $expectedUrl,
            $loginUrl
        );
    }

    public function testAuthorizeUrlWhenNoChannelId()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessageRegExp('#No client_id provided#');

        $lineLogin = new LINELogin(new DummyHttpClient($this, function () {
            return [];
        }), []);
        $lineLogin->makeAuthorizeUrl('http://example.com', 'profile openid email', 'testnonce', []);
    }

    public function testRequestToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/token', $url);
            $testRunner->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $header);

            $testRunner->assertEquals('authorization_code', $data['grant_type']);
            $testRunner->assertEquals('http://example.com', $data['redirect_uri']);
            $testRunner->assertEquals('1234567890abcde', $data['code']);
            $testRunner->assertEquals('1234567890', $data['client_id']);
            $testRunner->assertEquals('CHANNEL-SECRET', $data['client_secret']);

            return [
                'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
                'expires_in' => 2592000,
                'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
                'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
                'scope' => 'profile',
                'token_type' => 'Bearer',
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = $lineLogin->requestToken('http://example.com', '1234567890abcde');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $token->getAccessToken());
        $this->assertEquals(2592000, $token->getExpiredIn());
        $this->assertEquals('eyJhbGciOiJIUzI1NiJ9', $token->getIdToken());
        $this->assertEquals('Aa1FdeggRhTnPNNpxr8p', $token->getRefreshToken());
        $this->assertEquals('profile', $token->getScope());
        $this->assertEquals('Bearer', $token->getTokenType());
    }

    public function testRequestTokenWithCodeVerifier()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/token', $url);
            $testRunner->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $header);

            $testRunner->assertEquals('authorization_code', $data['grant_type']);
            $testRunner->assertEquals('http://example.com', $data['redirect_uri']);
            $testRunner->assertEquals('1234567890abcde', $data['code']);
            $testRunner->assertEquals('1234567890', $data['client_id']);
            $testRunner->assertEquals('CHANNEL-SECRET', $data['client_secret']);
            $testRunner->assertEquals('levySNAolAX1Jw9I89fffAmT7cQXW1Igi07ShIKNn3P', $data['code_verifier']);

            return [
                'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
                'expires_in' => 2592000,
                'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
                'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
                'scope' => 'profile',
                'token_type' => 'Bearer',
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = $lineLogin->requestToken(
            'http://example.com',
            '1234567890abcde',
            'levySNAolAX1Jw9I89fffAmT7cQXW1Igi07ShIKNn3P'
        );

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $token->getAccessToken());
        $this->assertEquals(2592000, $token->getExpiredIn());
        $this->assertEquals('eyJhbGciOiJIUzI1NiJ9', $token->getIdToken());
        $this->assertEquals('Aa1FdeggRhTnPNNpxr8p', $token->getRefreshToken());
        $this->assertEquals('profile', $token->getScope());
        $this->assertEquals('Bearer', $token->getTokenType());
    }

    public function testVerifyToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/verify', $url);

            $testRunner->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $data['access_token']);

            return [
                'scope' => 'profile',
                'client_id' => '1440057261',
                'expires_in' => 2591659,
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $response = $lineLogin->verifyToken($token);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getHTTPStatus());
        $this->assertEquals('profile', $response->getJSONDecodedBody()['scope']);
        $this->assertEquals('1440057261', $response->getJSONDecodedBody()['client_id']);
        $this->assertEquals(2591659, $response->getJSONDecodedBody()['expires_in']);
    }

    public function testRefreshToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/token', $url);
            $testRunner->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $header);

            $testRunner->assertEquals('refresh_token', $data['grant_type']);
            $testRunner->assertEquals('Aa1FdeggRhTnPNNpxr8p', $data['refresh_token']);
            $testRunner->assertEquals('1234567890', $data['client_id']);
            $testRunner->assertEquals('CHANNEL-SECRET', $data['client_secret']);

            return [
                'token_type' => 'Bearer',
                'scope' => 'profile',
                'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw...',
                'expires_in' => 2591977,
                'refresh_token' => '8iFFRdyxNVNLWYeteMMJ',
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $token = $lineLogin->refreshToken($token);

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('Bearer', $token->getTokenType());
        $this->assertEquals('profile', $token->getScope());
        $this->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw...', $token->getAccessToken());
        $this->assertEquals(2591977, $token->getExpiredIn());
        $this->assertEquals('8iFFRdyxNVNLWYeteMMJ', $token->getRefreshToken());
        $this->assertEquals(null, $token->getIdToken());
    }

    public function testRevokeToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/revoke', $url);
            $testRunner->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $header);

            $testRunner->assertEquals('bNl4YEFPI/hjFWhTqexp4MuEw5YPs...', $data['access_token']);
            $testRunner->assertEquals('1234567890', $data['client_id']);
            $testRunner->assertEquals('CHANNEL-SECRET', $data['client_secret']);

            return [];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $response = $lineLogin->revokeToken($token);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getHTTPStatus());
        $this->assertEquals([], $response->getJSONDecodedBody());
    }

    public function testVerifyIdToken()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/oauth2/v2.1/verify', $url);
            $testRunner->assertEquals(['Content-Type' => 'application/x-www-form-urlencoded'], $header);

            $testRunner->assertEquals('eyJhbGciOiJIUzI1NiJ9', $data['id_token']);
            $testRunner->assertEquals('1234567890', $data['client_id']);

            return [
                'iss' => 'https://access.line.me',
                'sub' => 'U1234567890abcdef1234567890abcdef',
                'aud' => '1234567890',
                'exp' => 1504169092,
                'iat' => 1504263657,
                'nonce' => '0987654asdf',
                'amr' => [
                    'pwd',
                    'linesso',
                    'lineqr',
                ],
                'name' => 'Taro Line',
                'picture' => 'https://sample_line.me/aBcdefg123456',
                'email' => 'taro.line@example.com',
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $idTokenPayload = $lineLogin->verifyIdToken($token);

        $this->assertInstanceOf(IdTokenPayload::class, $idTokenPayload);
        $this->assertEquals('https://access.line.me', $idTokenPayload->iss);
        $this->assertEquals('U1234567890abcdef1234567890abcdef', $idTokenPayload->sub);
        $this->assertEquals('1234567890', $idTokenPayload->aud);
        $this->assertEquals(1504169092, $idTokenPayload->exp);
        $this->assertEquals(1504263657, $idTokenPayload->iat);
        $this->assertEquals('0987654asdf', $idTokenPayload->nonce);
        $this->assertEquals([
            'pwd',
            'linesso',
            'lineqr',
        ], $idTokenPayload->amr);
        $this->assertEquals('Taro Line', $idTokenPayload->name);
        $this->assertEquals('https://sample_line.me/aBcdefg123456', $idTokenPayload->picture);
        $this->assertEquals('taro.line@example.com', $idTokenPayload->email);
        $this->assertEquals(null, $idTokenPayload->authTime);
    }

    public function testGetUserProfile()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/profile', $url);
            $testRunner->assertEquals(['Authorization' => 'Bearer bNl4YEFPI/hjFWhTqexp4MuEw5YPs...'], $header);

            return [
                'userId' => 'U4af4980629...',
                'displayName' => 'Brown',
                'pictureUrl' => 'https://profile.line-scdn.net/abcdefghijklmn',
                'statusMessage' => 'Hello, LINE!',
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $userProfile = $lineLogin->getUserProfile($token);

        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertEquals('U4af4980629...', $userProfile->getUserId());
        $this->assertEquals('Brown', $userProfile->getDisplayName());
        $this->assertEquals('https://profile.line-scdn.net/abcdefghijklmn', $userProfile->getPictureUrl());
        $this->assertEquals('Hello, LINE!', $userProfile->getStatusMessage());
    }

    public function testGetFriendshipStatus()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $header) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('GET', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/friendship/v1/status', $url);
            $testRunner->assertEquals(['Authorization' => 'Bearer bNl4YEFPI/hjFWhTqexp4MuEw5YPs...'], $header);

            return [
                'friendFlag' => true,
            ];
        };

        $lineLogin = new LINELogin(new DummyHttpClient($this, $mock), [
            'clientId' => '1234567890',
            'clientSecret' => 'CHANNEL-SECRET'
        ]);

        $token = new Token([
            'access_token' => 'bNl4YEFPI/hjFWhTqexp4MuEw5YPs...',
            'expires_in' => 2592000,
            'id_token' => 'eyJhbGciOiJIUzI1NiJ9',
            'refresh_token' => 'Aa1FdeggRhTnPNNpxr8p',
            'scope' => 'profile',
            'token_type' => 'Bearer',
        ]);

        $friendshipStatus = $lineLogin->getFriendshipStatus($token);

        $this->assertTrue($friendshipStatus);
    }
}