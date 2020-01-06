<?php

namespace App\Http\Requests;

use App\Event;
use App\ApiUser;
use App\Http\Requests\Request;
use App\Services\ApiKeyService;
use App\Services\EventsService;

class AddEventRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // this one not checked, by the validation rules, should move to policy
        $data = $this->only(['object','action','version']);
        $apiuser = app(ApiKeyService::class)->getApiUser($this->header('apiKey'));

        // check if event created by apiuser?
        $event = app(EventsService::class)->getEvent($data['object'],$data['action'],$data['version']);
        return ($event->apiUser==$apiuser->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'object' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'version' => 'required|string|max:255',
        ];
    }

    /**
     * OVERRIDE: Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    /*
    public function response(array $errors)
    {
        $str = "";
        foreach ($errors as $fld=>$msg) {
            $tmp = implode(", ",$msg);
            $str .= ($str!=""? ",":"").$tmp." ";
        }
        $str = trim($str);
        throw new \Exception($str, 422);
    }*/

    protected function failedAuthorization()
    {
        throw new \Exception("Forbidden ApiUser to create this Event", 403);
    }
}
