<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\RabbitMQService;

class QueuesController extends Controller
{
    public function overview()
    {
    	$response = RabbitMQService::getOverview();
    	return response()->json($response);
    }

    public function nodes()
    {
    	$response = RabbitMQService::getNodes();
    	return response()->json($response);
    }

    public function exchanges()
    {
    	$resp = RabbitMQService::getExchanges();
    	return response()->json($resp);
    }

    public function queues()
    {
    	$resp = RabbitMQService::getQueues();
    	return response()->json($resp);
    }
}
