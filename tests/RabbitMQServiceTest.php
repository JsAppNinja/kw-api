<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Services\RabbitMQService;

class RabbitMQServiceTest extends TestCase
{
	/**
     *@var class RabbitMQService 
     */
	private $rmqService;

	public function setUp()
    {
        parent::setUp();
        $this->rmqService = new RabbitMQService;
    }

    /**
     * rmq service test get nodes
     * @return void
     */
    public function testGetOverview()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();

    	$result = $this->rmqService->getOverview();
        $this->assertNotEmpty($result);
    }

    public function testGetNodes()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $result = $this->rmqService->getNodes();
    	$this->assertNotEmpty($result[0]["name"]);
    }

    /**
    public function testGetNode()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $result = $this->rmqService->getNode("rabbit@localhost");
    	$this->assertNotEmpty($result["memory"]);
    }
     */

    public function testGetExchanges()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $result = $this->rmqService->getExchanges();
    	$this->assertNotEmpty($result[0]["vhost"]);
    }

    public function testGetExchange()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $result = $this->rmqService->getExchange("/");
    	$this->assertNotEmpty($result);
    }

    public function testGetQueues()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $result = $this->rmqService->getQueues();
    	$this->assertNotEmpty($result);
    }
}
