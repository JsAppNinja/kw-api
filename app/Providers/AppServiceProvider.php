<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EventsService;
use App\Services\ApiKeyService;
use App\Services\LogActivityService;
use App\Event;
use App\Subscriber;
use App\ApiUser;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ApiKeyService::class, function ($app) {
             return new ApiKeyService();
        });

        $this->app->bind(LogActivityService::class, function($app) {
            return new LogActivityService();
        });
    }
}
