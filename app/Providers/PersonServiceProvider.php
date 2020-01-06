<?php
namespace App\Providers;


use App\Services\PersonService;
use Illuminate\Support\ServiceProvider;

class PersonServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PersonService::class, function (){
            return new PersonService(config('services.fullcontact.key'));
        });
    }
}