<?php

namespace EJLin\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string randomString(int $length)
 * @method static string makePKCECodeVerifier(int $length = 0)
 * @method static string makePKCECodeChallenge(string $codeVerifier)
 *
 * @see \EJLin\LINELogin\Helper
 */
class LINELoginHelper extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'line-login-helper';
    }
}