<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        
        //HttpExceptionInterface inheritor throws an error, it ignores its status code
        if($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface){
            $errorCode = $e->getStatusCode();
            $errorMessage = $e->getMessage();
            if (empty($errorMessage)) $errorMessage = \Illuminate\Http\Response::$statusTexts[$errorCode];
        } else {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
        }

        $error = [
            'message' => $errorMessage?$errorMessage:"Server Fail", 
            'code'=>$errorCode,
        ];

        if (env('APP_DEBUG')) {
            $error['trace'] = $e->getTrace();
        }

        if ($error['code']>200 && $error['code']<600) {
            return $this->jsonResponse($error,$error['code']);    
        } else {
            return $this->jsonResponse($error);    
        }
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload=null, $statusCode=400)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }
}
