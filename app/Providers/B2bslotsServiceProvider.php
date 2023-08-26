<?php

namespace App\Providers;

use App\Services\B2bslots\Client;
use Illuminate\Support\ServiceProvider;

class B2bslotsServiceProvider extends ServiceProvider
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
                config('services.b2bslots.url'),
                intval(config('services.b2bslots.operator_id'))
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
