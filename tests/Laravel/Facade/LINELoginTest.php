<?php

namespace EJLin\Tests\Laravel\Facade;

use EJLin\Laravel\Facades\LINELogin;
use EJLin\Laravel\LINELoginServiceProvider;
use Orchestra\Testbench\TestCase;

class LINELoginTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LINELoginServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LINELogin' => LINELogin::class
        ];
    }

    public function testConfigLoaded()
    {
        $this->assertEquals('test_client_id', config('line-login.client_id'));
        $this->assertEquals('test_client_secret', config('line-login.client_secret'));
    }
}
