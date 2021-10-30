<?php

namespace EJLin\Tests\LINELogin;

use EJLin\LINELogin\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testRandomString()
    {
        $string = Helper::randomString(50);

        $this->assertTrue(is_string($string));
        $this->assertEquals(50, strlen($string));
    }

    public function testPKCECodeVerifier()
    {
        $codeVerifier = Helper::makePKCECodeVerifier(0);

        $this->assertTrue(is_string($codeVerifier));
        $this->assertLessThanOrEqual(128, strlen($codeVerifier));
        $this->assertGreaterThanOrEqual(43, strlen($codeVerifier));
    }

    public function testPKCECodeChallenge()
    {
        $codeVerifier = 'levySNAolAX1Jw9I89fffAmT7cQXW1Igi07ShIKNn3P';

        $codeChallenge = Helper::makePKCECodeChallenge($codeVerifier);

        $this->assertTrue(is_string($codeChallenge));
        $this->assertEquals('9ETE40L4xrAd6PbzWAtwz_Gkj-rNjEPZUwiBQ_u8e2I', $codeChallenge);
    }
}