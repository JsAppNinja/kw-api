<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

use Log;

class CreateListRequest extends Request
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
            'name' => 'required|string|max:255',
            'router' => 'required|string|max:255',
            'hash' => 'required|string|max:8'
        ];
    }

}
