<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\RoutingList;
use App\Services\ListService;
use App\Contracts\ListServiceInterface;
use Log;

class RoutingListServiceProvider extends ServiceProvider
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
        $this->app->bind("App\Contracts\ListServiceInterface", function ($app) {
            $list = $this->app->request->route('lists');
            if ($list) {
                return new ListService($list);
            } else {
                return new ListService(new RoutingList);
            }
        });
    }
}
