<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Middleware\LogApiCall;
use App\LogActivity;


class LogApiCallMiddlewareTest extends TestCase
{

    protected $request;
    protected $response;
    protected $middleware;

    public function setUp()
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class);
        $this->response = Mockery::mock(JsonResponse::class);
        $this->response->headers = Mockery::mock(Symfony\Component\HttpFoundation\ResponseHeaderBag::class);
        $this->middleware = new LogApiCall;
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogActivityMiddlewareSuccess()
    {   
        $path ='v1/test/log_api_call_middleware_test' . rand();

        $this->response->headers->shouldReceive('get')->andReturn(null);
        $this->request->shouldReceive('server')->andReturn(['wew' => 'wow']);
        $this->request->shouldReceive('header')->with('apiKey')->andReturn('testApiKey');
        $this->request->shouldReceive('path')->andReturn($path);
        $this->request->shouldReceive('method')->andReturn('GET');
        $this->request->shouldReceive('all')->andReturn([]);
        $this->response->shouldReceive('getContent')->andReturn(json_encode(['foo' => 'bar']));
        $this->response->shouldReceive('getStatusCode')->andReturn(200);
        $this->middleware->handle($this->request, function() {});
        $this->middleware->terminate($this->request, $this->response);

        $this->seeInDatabase('log_activities', ['method_called' => $path]);

        // Delete data
        LogActivity::where(['method_called' => $path])->delete();
    }
}
