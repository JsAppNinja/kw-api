<?php

namespace App\Services;

use Cache;
use App\ApiUser;
use GuzzleHttp\Client;

class RabbitMQService
{
    /**
     * http client to do request
     *
     * @var GuzzleHttp\Client
     */
    private static $client;

    public static function client()
    {
        if (self::$client) return self::$client;

        self::$client = new \GuzzleHttp\Client();
        return self::$client;
    }

    /**
     * set message queue host base on env settings
     */
    public static function host()
    {
        return "http://".env('AMQP_HOST','localhost').":".env('AMQP_API_PORT','15672');
    }

    /**
     * set message queue auth base on env settings
     */
    public static function auth()
    {
        return ["auth"=>[env("AMQP_LOGIN","guest"),env("AMQP_PASSWORD","guest")]];
    }

    public static function check()
    {
        if (env("QUEUE_DRIVER")!="rabbitmq") return false;
        return true;
    }

    /**
     * get overview rabbitMQ
     * /api/overview
     * @return JsonObject
     */
    public static function getOverview()
    {
        if (!self::check()) return [];

        $client = self::client();
        $res = $client->get(self::host()."/api/overview",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

	/**
     * get list of nodes
     * /api/nodes
     * @return JsonObject
     */
    public static function getNodes()
    {   
        if (!self::check()) return [];   
    	$client = self::client();
        $res = $client->get(self::host()."/api/nodes",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get node detail by name
     * /api/nodes/<name>?memory=true&binary=true
     * @param string $name
     * @return JsonObject
     */
    public static function getNode($name)
    {
        if (!self::check()) return [];

        $name = urlencode($name);
        $client = self::client();
        $res = $client->get(self::host()."/api/nodes/$name?memory=true&binary=true",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get list of exchanges message stats
     * /api/exchanges/
     * @return JsonObject
     */
    public static function getExchanges()
    {
        if (!self::check()) return [];

        $client = self::client();
        $res = $client->get(self::host()."/api/exchanges",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get exchange detail by vhost message stats
     * /api/exchanges/$vhost
     * @param string $vhost
     * @return JsonObject
     */
    public static function getExchange($vhost) 
    {
        if (!self::check()) return [];

        $vhost = urlencode($vhost);
        $client = self::client();
        $res = $client->get(self::host()."/api/exchanges/$vhost",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get queues list message stats
     * /api/queues/
     * @return JsonObject
     */
    public static function getQueues()
    {
        if (!self::check()) return [];

        $client = self::client();
        $res = $client->get(self::host()."/api/queues",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get queues message stats
     * /api/queues/$vhost
     * @param string $vhost
     * @return JsonObject
     */
    public static function getQueue($vhost)
    {
        if (!self::check()) return [];

        $vhost = urlencode($vhost);
        $client = self::client();
        $res = $client->get(self::host()."/api/queues/$vhost",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }

    /**
     * get queues detail stats
     * /api/queues/(vhost)/(name)
     * @param string $vhost
     * @param string $name
     * @return JsonObject
     */
    public static function getQueueDetail($vhost,$name)
    {
        if (!self::check()) return [];

        $client = self::client();
        $vhost = urlencode($vhost);
        $name = urlencode($name);
        $res = $client->get(self::host()."/api/queues/$vhost/$name",self::auth());
        $body = $res->getBody()->getContents();

        return json_decode($body,true);
    }
}