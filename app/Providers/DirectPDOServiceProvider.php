<?php
/**
 * Created by PhpStorm.
 * User: vanyaz158
 * Date: 15.11.16
 * Time: 2:23
 */

namespace app\Providers;

use Illuminate\Support\ServiceProvider;


class DirectPDOServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('\PDO', function () {
            $conStr = $this->getConnectionString();
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');

            return new \PDO($conStr, $username, $password);
        });
    }

    public function provides()
    {
        return [\PDO::class];
    }

    protected function getConnectionString()
    {
        $connectionType = env('DB_CONNECTION');
        $host = env('DB_HOST');
        $port = env('DB_PORT');

        return "$connectionType:host=$host;port=$port";
    }
}
