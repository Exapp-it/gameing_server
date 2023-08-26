<?php

namespace App\Providers;

use App\Services\TomHorn\Client;
use Illuminate\Support\ServiceProvider;

class TomHornServiceProvider extends ServiceProvider
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
                config('services.tom_horn.integration_service'),
                config('services.tom_horn.report_service'),
                config('services.tom_horn.partner_id'),
                config('services.tom_horn.secret_key'),
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
