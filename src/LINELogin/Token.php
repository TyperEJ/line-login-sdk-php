<?php

namespace EJLin\LINELogin;

class Token
{
    /** @var string */
    private $accessToken;
    /** @var string */
    private $expiredIn;
    /** @var string */
    private $idToken;
    /** @var string */
    private $refreshToken;
    /** @var string */
    private $scope;
    /** @var string */
    private $tokenType;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->accessToken = $data['access_token'];
        $this->expiredIn = $data['expires_in'];
        $this->refreshToken = $data['refresh_token'];
        $this->scope = $data['scope'];
        $this->tokenType = $data['token_type'];
        $this->idToken = $data['id_token'] ?? null; // Refresh token probably lost id token.
    }

    /**
     * @param array $data
     * @return Token
     */
    public static function parseToTokenObject(array $data)
    {
        return new Token($data);
    }

    /**
     * @return mixed|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return mixed|string
     */
    public function getExpiredIn()
    {
        return $this->expiredIn;
    }

    /**
     * @return mixed|string
     */
    public function getIdToken()
    {
        return $this->idToken;
    }

    /**
     * @return mixed|string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return mixed|string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return mixed|string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }
}