<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\ApiUser;
use Illuminate\Http\Request;
use App\Http\Requests\ApiUserRequest;

class ApiUserPolicy
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

    public function update(Request $request, ApiUser $apiUser) 
    {
        return true;
    }

    public function delete(Request $request, ApiUser $apiUser) 
    {
        return true;
    }

    public function create(Request $request, ApiUser $apiUser)
    {
        return true;
    }
}
