<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RegisterEventRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'jsonSchema' => 'required|string',
        ];
    }

}
