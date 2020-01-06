<?php

namespace App\Services;

use App\LogActivity;
use App\ApiUser;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\LogActivityJob;
use App\Services\ApiKeyService;

class LogActivityService {
    
    /**
     * Log Activity
     *
     * @param string $routeActionName Route action name
     * @param \Illuminate\Http\Request $request  Laravel request object
     * @param \Symfony\Component\HttpFoundation\Response $response Response object
     *
     * @return void
     */
    public function log($routeActionName, Request $request, Response $response)
    {
        $apiKey = $request->header('apiKey');
        //$apiUser = ApiUser::where(['apiKey' => $apiKey])->first();
        $apiUser = app(ApiKeyService::class)->getApiUser($apiKey);
        $apiUserId = !empty($apiUser->id) ? $apiUser->id : null;
        
        $data = [
                'api_key' => $apiKey, 
                'api_user_id' => $apiUserId,
                'route_action_name' => $routeActionName,
                'method_called' => $this->parseMethodCalled($request),
                'request_method' => $request->method(),
                'request_object' => json_encode($this->parseRequestObject($request)),
                'response_object' => json_encode($this->parseResponseObject($response)),
                'response_body' => $response->getContent(),
                'response_status_code' => $response->getStatusCode(),
            ];
            
        // Fill log data to database
        \dispatch(new LogActivityJob($data));
    }

    /**
     * Parse method called from request
     * 
     * @param \Illuminate\Http\Request $request  Laravel request object
     * 
     * @return string Return request path
     */
    protected function parseMethodCalled(Request $request)
    {
        return $request->path();
    }

    /**
     * Parse request object
     * 
     * @param \Illuminate\Http\Request $request  Laravel request object
     *
     * @return array Return request object
     */
    protected function parseRequestObject(Request $request)
    {
        return  ['parameters' => $request->all(), 'server_info' => $request->server()];
    }

    /**
     * Parse response object
     *
     * @param \Symfony\Component\HttpFoundation\Response $response Response object
     *
     * @return array Return response object
     */
    protected function parseResponseObject(Response $response)
    {
        return [
            'headers' => $this->parseResponseHeaders($response),
            'body' => $response->getContent(),
            'status_code' => $response->getStatusCode(),
        ];
    }

    /**
     * Parse response header
     *
     * @param \Symfony\Component\HttpFoundation\Response $response Response object
     * 
     * @return array Return response headers
     */
    protected function parseResponseHeaders(Response $response)
    {
        return [
            \strtok($response->__toString(), "\r\n"),
            'Content-Type' => $response->headers->get('Content-Type'),
            'Cache-Control' => $response->headers->get('Cache-Control'),
            'Date' => $response->headers->get('Date'),
        ];
    }



}