<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mockery\CountValidator\Exception;
use App\Services\EventsService;

class EventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EventsService::class, function () {
             return new EventsService();
        });
    }

}
