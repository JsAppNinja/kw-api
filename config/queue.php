<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'iron'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'ttr' => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue' => 'your-queue-name',
            'region' => 'us-east-1',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'expire' => 60,
        ],

        'iron' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-eu-west-1-1.iron.io',
            'token'   => 'VvnsI8DxiPtpupJ0PNJE',
            'project' => '576f3c39b7409e0007126988',
            'queue'   => 'events',
            'encrypt' => true,
            'timeout' => 60
        ],

        'rabbitmq' => [
            'driver'                => 'rabbitmq',
            'host'                  => env('AMQP_HOST', '127.0.0.1'),
            'port'                  => env('AMQP_PORT', 5672),
            'vhost'                 => env('AMQP_VHOST', '/'),
            'login'                 => env('AMQP_LOGIN', 'guest'),
            'password'              => env('AMQP_PASSWORD', 'guest'),
            'queue'                 => env('AMQP_QUEUE'), // name of the default queue,
            'exchange_declare'      => env('AMQP_EXCHANGE_DECLARE', true), // create the exchange if not exists
            'queue_declare_bind'    => env('AMQP_QUEUE_DECLARE_BIND', true), // create the queue if not exists and bind to the exchange
            'queue_params'          => [
                'passive'           => env('AMQP_QUEUE_PASSIVE', false),
                'durable'           => env('AMQP_QUEUE_DURABLE', true),
                'exclusive'         => env('AMQP_QUEUE_EXCLUSIVE', false),
                'auto_delete'       => env('AMQP_QUEUE_AUTODELETE', false),
            ],
            'exchange_params' => [
                'name'        => env('AMQP_EXCHANGE_NAME', null),
                'type'        => env('AMQP_EXCHANGE_TYPE', 'direct'), // more info at http://www.AMQP.com/tutorials/amqp-concepts.html
                'passive'     => env('AMQP_EXCHANGE_PASSIVE', false),
                'durable'     => env('AMQP_EXCHANGE_DURABLE', false), // the exchange will survive server restarts
                'auto_delete' => env('AMQP_EXCHANGE_AUTODELETE', false),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
