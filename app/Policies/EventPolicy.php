<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;
use App\Event;
use App\Services\ApiKeyService;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(Request $request,Event $event) 
    {
        $apiuser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));
        return ($event->apiUser==$apiuser->id);
    }

    public function delete(Request $request,Event $event) 
    {
        $apiuser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));
        return ($event->apiUser==$apiuser->id);
    }

    public function addEvent(Request $request,Event $event)
    {
        $apiuser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));
        return ($event->apiUser==$apiuser->id);
    }
}
