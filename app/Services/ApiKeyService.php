<?php

namespace App\Services;

use Cache;
use App\ApiUser;
use App\Services\ApiKeyService;

class ApiKeyService
{

	/**
     * get ApiUser data based on apiKey and cache
     * @param $apiKey string apiKey value
     * @return ApiUser
     */
    public function getApiUser($apiKey)
    {      
    	// had a draw back if someone abuse the system by requesting over and over with invalid apikey
    	$apiuser = ApiUser::where('apiKey', $apiKey)->rememberForever()->first();
    	return $apiuser;
    }

    public function forgetApiUser($apiKey)
    {
    	$key = ApiUser::class.".".$apiKey;
    	Cache::forget($key);
    }
}