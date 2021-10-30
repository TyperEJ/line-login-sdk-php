<?php

namespace EJLin\Tests\Laravel\Facade;

use EJLin\Laravel\Facades\LINELoginHelper;
use EJLin\Laravel\LINELoginServiceProvider;
use Orchestra\Testbench\TestCase;

class LINELoginHelperTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LINELoginServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LINELoginHelper' => LINELoginHelper::class
        ];
    }

    public function testRandomString()
    {
        $string = LINELoginHelper::randomString(50);

        $this->assertTrue(is_string($string));
        $this->assertEquals(50, strlen($string));
    }
}
