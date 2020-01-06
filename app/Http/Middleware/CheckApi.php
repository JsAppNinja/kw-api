<?php

namespace App\Http\Middleware;

use App\ApiUser;
use Closure;
use App\Services\ApiKeyService;

class CheckApi
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
        //Check that the user has access to hit the API
        $apiuser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));
        
        if ($apiuser && $apiuser->isActive==1) {
            return $next($request);
        }

        throw new \Exception('Problem with your account or no valid apiKey.',401);
    }
}
