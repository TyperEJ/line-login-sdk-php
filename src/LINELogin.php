<?php

namespace EJLin;

use EJLin\LINELogin\Exception\AuthenticationException;
use EJLin\LINELogin\HTTPClient;
use EJLin\LINELogin\IdTokenPayload;
use EJLin\LINELogin\Response;
use EJLin\LINELogin\Token;
use EJLin\LINELogin\UserProfile;

class LINELogin
{
    const DEFAULT_ENDPOINT_BASE = 'https://api.line.me';
    const AUTHORIZE_URL = 'https://access.line.me/oauth2/v2.1/authorize';

    /** @var string */
    private $clientSecret;
    /** @var string */
    private $clientId;
    /** @var string */
    private $endpointBase;
    /** @var string */
    private $authorizeUrl;
    /** @var HTTPClient */
    private $httpClient;

    /**
     * LINELogin constructor.
     *
     * @param HTTPClient $httpClient HTTP client instance to use API calling.
     * @param array $args Configurations.
     */
    public function __construct(HTTPClient $httpClient, array $args)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $args['clientId'] ?? null;
        $this->clientSecret = $args['clientSecret'] ?? null;

        $this->endpointBase = LINELogin::DEFAULT_ENDPOINT_BASE;
        $this->authorizeUrl = LINELogin::AUTHORIZE_URL;
    }

    /**
     * Generate line login authorize url.
     *
     * @see https://developers.line.biz/en/docs/line-login/integrate-line-login/#making-an-authorization-request
     * @param string $redirectUri Callback URL registered.
     * @param string $scope Permissions requested from the user. ex:'profile openid email'
     * @param string $state A unique alphanumeric string used to prevent cross-site request forgery (opens new window).
     * @param array $optionalArgs Optional args.
     * @return string
     * @throws AuthenticationException
     */
    public function makeAuthorizeUrl(
        string $redirectUri,
        string $scope,
        string $state,
        array  $optionalArgs = []
    )
    {
        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
        ];

        return implode('?', [
            $this->authorizeUrl,
            http_build_query(
                array_merge($params, $optionalArgs),
                '',
                '&',
                PHP_QUERY_RFC3986
            )
        ]);
    }

    /**
     * Generate line login PKCE authorize url.
     *
     * @see https://developers.line.biz/en/docs/line-login/integrate-line-login/#making-an-authorization-request
     * @param string $redirectUri Callback URL registered.
     * @param string $scope Permissions requested from the user. ex:'profile openid email'
     * @param string $state A unique alphanumeric string used to prevent cross-site request forgery (opens new window).
     * @param string $codeChallenge A code challenge derived from the code verifier.
     * @param array $optionalArgs Optional args.
     * @return string
     * @throws AuthenticationException
     */
    public function makePKCEAuthorizeUrl(
        string $redirectUri,
        string $scope,
        string $state,
        string $codeChallenge,
        array  $optionalArgs = []
    )
    {
        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        return implode('?', [
            $this->authorizeUrl,
            http_build_query(
                array_merge($params, $optionalArgs),
                '',
                '&',
                PHP_QUERY_RFC3986
            )
        ]);
    }

    /**
     * Request token.
     *
     * @see https://developers.line.biz/en/reference/line-login/#issue-access-token
     * @param string $redirectUri
     * @param string $code
     * @param string $codeVerifier
     * @return Token
     * @throws AuthenticationException
     */
    public function requestToken(string $redirectUri, string $code, string $codeVerifier = '')
    {
        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        if ($this->clientSecret == null) {
            throw new AuthenticationException('No client_secret provided.');
        }

        $params = [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        if ($codeVerifier) {
            $params['code_verifier'] = $codeVerifier;
        }

        $response = $this->httpClient->post(
            $this->endpointBase . '/oauth2/v2.1/token',
            $params,
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        return Token::parseToTokenObject($response->getJSONDecodedBody());
    }

    /**
     * Verify access token.
     *
     * @see https://developers.line.biz/en/reference/line-login/#verify-access-token
     * @param Token $token
     * @return Response
     */
    public function verifyToken(Token $token)
    {
        return $this->httpClient->get($this->endpointBase . '/oauth2/v2.1/verify', [
            'access_token' => $token->getAccessToken(),
        ]);
    }

    /**
     * Refresh access token.
     *
     * @see https://developers.line.biz/en/reference/line-login/#refresh-access-token
     * @param Token $token
     * @param bool $issuedThroughWebApp
     * @return Token
     * @throws AuthenticationException
     */
    public function refreshToken(Token $token, bool $issuedThroughWebApp = true)
    {
        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->getRefreshToken(),
            'client_id' => $this->clientId,
        ];

        if ($issuedThroughWebApp) {
            if ($this->clientSecret == null) {
                throw new AuthenticationException('No client_secret provided.');
            }

            $params['client_secret'] = $this->clientSecret;
        }

        $response = $this->httpClient->post(
            $this->endpointBase . '/oauth2/v2.1/token',
            $params,
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        return Token::parseToTokenObject($response->getJSONDecodedBody());
    }

    /**
     * Revoke access token.
     *
     * @see https://developers.line.biz/en/reference/line-login/#revoke-access-token
     * @param Token $token
     * @return Response
     * @throws AuthenticationException
     */
    public function revokeToken(Token $token)
    {
        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        if ($this->clientSecret == null) {
            throw new AuthenticationException('No client_secret provided.');
        }

        return $this->httpClient->post(
            $this->endpointBase . '/oauth2/v2.1/revoke',
            [
                'access_token' => $token->getAccessToken(),
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );
    }

    /**
     * Verify Id Token.
     *
     * @see https://developers.line.biz/en/reference/line-login/#verify-id-token
     * @param Token $token
     * @param array $optionalArgs
     * @return IdTokenPayload
     * @throws AuthenticationException
     */
    public function verifyIdToken(Token $token, array $optionalArgs = [])
    {
        if ($token->getIdToken() == null) {
            throw new AuthenticationException('No id_token provided.');
        }

        if ($this->clientId == null) {
            throw new AuthenticationException('No client_id provided.');
        }

        $params = [
            'id_token' => $token->getIdToken(),
            'client_id' => $this->clientId,
        ];

        $response = $this->httpClient->post(
            $this->endpointBase . '/oauth2/v2.1/verify',
            array_merge($params, $optionalArgs),
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        return IdTokenPayload::parseToIdTokenPayloadObject($response->getJSONDecodedBody());
    }

    /**
     * Get user profile.
     *
     * @see https://developers.line.biz/en/reference/line-login/#get-user-profile
     * @param Token $token
     * @return UserProfile
     */
    public function getUserProfile(Token $token)
    {
        $response = $this->httpClient->get(
            $this->endpointBase . '/v2/profile',
            [],
            [
                'Authorization' => "Bearer {$token->getAccessToken()}",
            ]
        );

        return UserProfile::parseToUserProfileObject($response->getJSONDecodedBody());
    }

    /**
     * Get friendship status.
     *
     * @see https://developers.line.biz/en/reference/line-login/#get-friendship-status
     * @param Token $token
     * @return bool
     */
    public function getFriendshipStatus(Token $token)
    {
        $response = $this->httpClient->get(
            $this->endpointBase . '/friendship/v1/status',
            [],
            [
                'Authorization' => "Bearer {$token->getAccessToken()}",
            ]
        );

        return $response->getJSONDecodedBody()['friendFlag'];
    }
}