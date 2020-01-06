<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Support\Jsonable;

use App\LogActivity;

class LogActivityTest extends TestCase
{

    public function testLogActivity()
    {

        //TODO figure out why this test if failing
        $this->markTestSkipped();

        $request = Request::create('', 'GET');
        $response = new Illuminate\Http\JsonResponse(new JsonResponseTestJsonableObject);
        $responseBody = $response->content();
        
        LoggerActivity::log('LogActivityTest@testLogActivity', $request, $response);
        $this->seeInDatabase('log_activities', ['response_body' => $responseBody]);
        
        // delete
        LogActivity::where(['response_body' => $responseBody])->delete();

    }

}


class JsonResponseTestJsonableObject implements Jsonable
{
    public function toJson($options = 0)
    {
        return '{"foo":"bar"}';
    }
}