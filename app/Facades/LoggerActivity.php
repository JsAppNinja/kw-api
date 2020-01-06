<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\LogActivityService;


class LoggerActivity extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LogActivityService::class;
    }
}