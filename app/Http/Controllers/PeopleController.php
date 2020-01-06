<?php
namespace App\Http\Controllers;


use App\Services\PersonService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PeopleController extends Controller
{
    /**
     * Provides user search by email. Called by GET method. Must receive 'email' query param. Returns json document
     * similar to that structure: https://api.fullcontact.com/v2/person.json?phone=+13037170414
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupEmail()
    {
        $email = \Request::query('email');
        if(empty($email)){
            throw new HttpException(400, "email query param is required");
        }
        
        //todo: if there will not be any processing with result, so it's better to add params to return json string
        $result = app(PersonService::class)->findByEmail($email);
        return response()->json($result);
    }

    /**
     * Provides user search by phone(and optionally, countryCode). Called by GET method. Must receive 'phone'
     * query param.
     * if mobile phone is not from US or CA, must receive also 'countryCode', either will return 404 response
     * Returns json document similar to that structure: https://api.fullcontact.com/v2/person.json?phone=+13037170414
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupPhone()
    {
        $phone = \Request::query('phone');
        if(empty($phone)){
            throw new HttpException(400, "phone query param is required");
        }
        $countryCode = \Request::query('countryCode');

        $result = app(PersonService::class)->findByPhone($phone, $countryCode);
        return response()->json($result);
    }

    /**
     * Provides single entry point for lookup methods(lookupPhone and lookupEmail). Called by GET method. Must
     * contain 'email' or 'phone' query parameter. Depends on what parameter provided, 
     * it will call lookupPhone(if phone parameter provided) or lookupEmail(if email parameter provided). If both 
     * provided, lookupEmail will be called.
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookup()
    {
        if(\Request::query('email') !== null){
            return $this->lookupEmail();
        }
        if(\Request::query('phone') !== null){
            return $this->lookupPhone();
        }

        throw new HttpException(400, "either email or phone query param is required");
    }
}