<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ApiUserRequest extends Request
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
        $rules = [
            "apiKey"=>"required|string|unique:api_users,apiKey|max:255",
            "company"=>"required|string|max:255",
            "application"=>"required|string|max:255",
            "email"=>"required|string|email|max:255"
        ];
        if ($this->method()=='PUT') {
            $rparams = $this->route()->parameters();
            $rules["apiKey"] = "required|string|unique:api_users,apiKey,".$rparams["api_users"]."|max:255";
        }
        return $rules;
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
