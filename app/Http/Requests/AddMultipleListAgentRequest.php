<?php

namespace App\Http\Requests;

use App\ApiUser;
use App\RoutingList;
use App\Services\ApiKeyService;
use App\Http\Requests\Request;

use Log;

class AddMultipleListAgentRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // this one not checked, by the validation rules, should move to policy
        $apiuser = ApiKeyService::getApiUser($this->header('apiKey'));
        $list = $this->route('lists');
        return ($list->api_user_id==$apiuser->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'agents' => 'required|array'
        ];

        $agents = $this->get('agents');
        if ($agents && is_array($agents)) {
            foreach( $agents as $key => $value) {
                $rules['agents.' . $key. '.agent_id'] = 'required|string|max:255';
                $rules['agents.' . $key. '.name'] = 'required|string|max:255';
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];
        $agents = $this->get('agents');
        if ($agents && is_array($agents)) {
            foreach( $agents as $key => $value)
            {
                $messages['agents.' . $key. '.agent_id'.'.max'] = 'agent_id for agent'. $key .' must be less than :max characters.';
                $messages['agents.' . $key. '.agent_id'.'.required'] = 'agent_id for agent'. $key .' is required.';

                $messages['agents.' . $key. '.name'.'.max'] = 'name for agent'. $key .'  must be less than :max characters.';
                $messages['agents.' . $key. '.name'.'.required'] = 'name for agent'. $key .' is required.';
            }
        }

        return $messages;
    }

    protected function failedAuthorization()
    {
        throw new \Exception("Forbidden ApiUser to create this ListAgent", 403);
    }
}
