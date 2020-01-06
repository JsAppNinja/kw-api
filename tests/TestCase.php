<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    
    /**
     * The base version(url)
     * example: v1, v2 e.t.c
     * @var string 
     */
    protected $v = "v1";
         
    /**
     *@var array 
     */
    protected $jsonSchema = [
                              '$schema' => 'http://json-schema.org/draft-04/schema#',
                              'type'    => 'object',
                              'properties' =>[
                                     'object'    => ['type'=>'string'],   
                                     'action'    => ['type'=>'string'],
                                     'createdAt' => ['type'=>'string']                                  
                              ],
                              'required'=> ['object','action','createdAt']
                            ];
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
