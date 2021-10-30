<?php

namespace EJLin\LINELogin;

class Helper
{
    /**
     * Generate random string.
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function randomString(int $length)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Make PKCE code verify.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7636#section-4.1
     * @param int $length A minimum length of 43 characters and a maximum length of 128 characters.
     * @return string
     * @throws \Exception
     */
    public static function makePKCECodeVerifier(int $length = 0)
    {
        if ($length < 43 || $length > 128) {
            $length = rand(43, 128);
        }

        return self::randomString($length);
    }

    /**
     * Make PKCE code challenge.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7636#section-4.2
     * @param string $codeVerifier
     * @return string
     */
    public static function makePKCECodeChallenge(string $codeVerifier)
    {
        return strtr(rtrim(
            base64_encode(hash('sha256', $codeVerifier, true))
            , '='), '+/', '-_');
    }
}