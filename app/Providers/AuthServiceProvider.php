<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind(\Lcobucci\JWT\ValidationData::class, $this->getDataCallback());
    }

    public function provides()
    {
        return [\Lcobucci\JWT\ValidationData::class];
    }

    protected function getDataCallback()
    {
        return function () {
            $data = new \Lcobucci\JWT\ValidationData();
            $host = env('APP_HOST');

            $data->setIssuer($host);
            $data->setAudience($host);

            return $data;
        };
    }
}
