<?php

namespace App\Providers;

use App\Services\Mancala\Client;
use Illuminate\Support\ServiceProvider;

class MancalaServiceProvider extends ServiceProvider
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
                config('services.mancala.uri'),
                config('services.mancala.guid'),
                config('services.mancala.key'),
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
