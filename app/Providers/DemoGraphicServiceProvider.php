<?php
namespace App\Providers;


use App\Services\PersonService;
use Illuminate\Support\ServiceProvider;

class DemoGraphicServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DemoGraphicService::class, function (){
            return new DemoGraphicService();
        });
    }
}