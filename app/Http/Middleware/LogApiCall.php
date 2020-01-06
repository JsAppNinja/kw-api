<?php

namespace App\Http\Middleware;

use Route;
use Closure;
use LoggerActivity;

class LogApiCall
{



    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }


    /**
     * Log request and response before terminate
     */
    public function terminate($request, $response)
    {
        $actionName = !empty(Route::getCurrentRoute()) ? Route::getCurrentRoute()->getActionName() : '';
        LoggerActivity::log($actionName, $request, $response);
    }


}
