<?php

namespace App\Providers;

use App\Services\Expay\Client;
use Illuminate\Support\ServiceProvider;

class ExpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            return new Client(
                config('services.expay.uri'),
                config('services.expay.public'),
                config('services.expay.private'),
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
