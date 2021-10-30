<?php

namespace EJLin\Laravel\Facades;

use EJLin\LINELogin\IdTokenPayload;
use EJLin\LINELogin\Response;
use EJLin\LINELogin\Token;
use EJLin\LINELogin\UserProfile;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string makeAuthorizeUrl(string $channel, string $scope, string $state, array $optionalArgs = [])
 * @method static string makePKCEAuthorizeUrl(string $channel, string $scope, string $state, string $codeChallenge, array $optionalArgs = [])
 * @method static Token requestToken(string $redirectUri, string $code, string $codeVerifier = '')
 * @method static Response verifyToken(Token $token)
 * @method static Token refreshToken(Token $token, bool $issuedThroughWebApp = true)
 * @method static Response revokeToken(Token $token)
 * @method static IdTokenPayload verifyIdToken(Token $token, array $optionalArgs = [])
 * @method static UserProfile getUserProfile(Token $token)
 * @method static bool getFriendshipStatus(Token $token)
 *
 * @see \EJLin\LINELogin
 */
class LINELogin extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'line-login';
    }
}