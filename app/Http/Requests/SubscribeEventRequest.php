<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubscribeEventRequest extends Request
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
            'endPoint' => 'required|string|max:255',
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
}
