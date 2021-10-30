<?php

namespace EJLin\Laravel;

use EJLin\LINELogin;
use EJLin\LINELogin\GuzzleHTTPClient;
use EJLin\LINELogin\Helper;
use Illuminate\Support\ServiceProvider;

class LINELoginServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/line-login.php',
            'line-login'
        );

        $this->app->singleton('line-login-helper',function(){
            return new Helper();
        });

        $this->app->bind('line-login-http-client', function () {
            return new GuzzleHTTPClient();
        });

        $this->app->bind('line-login', function ($app) {
            $httpClient = $app->make('line-login-http-client');
            return new LINELogin($httpClient, [
                'clientId' => config('line-login.client_id'),
                'clientSecret' => config('line-login.client_secret'),
            ]);
        });
    }
}