<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * OVERRIDE: Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(array $errors)
    {
        $str = "";
        foreach ($errors as $fld=>$msg) {
            $tmp = implode(", ",$msg);
            $str .= ($str!=""? ",":"").$tmp." ";
        }
        $str = trim($str);
        throw new \Exception($str, 422);
    }
}
