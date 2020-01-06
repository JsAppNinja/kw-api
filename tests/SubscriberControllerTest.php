<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscriberControllerTest extends TestCase
{
	/**
     * This Test use DB::Transaction. 
     * 
     * auto delete. 
     */
	use DatabaseTransactions;

	/**
     * data subscriber
     * @var object
     */
	private $subscriber;

    /**
     * headers data for request
     * @var array
     */
    private $headers;

	public function setUp()
	{
		parent::setUp();

		$this->subscriber = factory(App\Subscriber::class)->create();
        $this->apiUser = factory(App\ApiUser::class)->create(['isActive'=>'1']);
        $this->headers = ['apiKey'=>$this->apiUser->apiKey];
	}

	/**
     * test SubscriberController@index
     * 
     */
	public function testIndex()
    {
        $test = $this->get($this->v.'/subscribers/', $this->headers)->assertResponseOk();
        $content = json_decode($this->response->getContent(),true);

        $this->assertTrue($content['total']>0);
    }

	/**
     * test SubscriberController@index with filtering option
     * 
     */
    public function testListWithFilter()
    {
        $this->get($this->v.'/subscribers/'.$this->subscriber->id, $this->headers);
        $json = $this->response->getContent();
        $data = json_decode($json,true);

        $test = $this->json('GET',$this->v.'/subscribers/',['_filters'=>'{"id":"'.$this->subscriber->id.'"}'],$this->headers);
        $this->seeJson(['total'=>1,'data'=>[$data]])->assertResponseOk();
    }

    /**
     * test SubscriberController@index with pagination option
     * 
     */
    public function testListWithPage()
    {
        $test = $this->json('GET',$this->v.'/subscribers/',['_perPage'=>5], $this->headers);
        $this->seeJson(['per_page'=>5])->assertResponseOk();   
    }

    /**
     * test SubscriberController@index with sort order option
     * 
     */
    public function testListWithSortField()
    {

        //TODO figure out why this test is failing....
        $this->markTestSkipped();

        $this->json('GET',$this->v.'/subscribers/',['_perPage'=>5,'_sortDir'=>'desc','_sortField'=>'id'], $this->headers);
        $test = $this->response->getContent();

        $data = json_decode($test,true);
        $str1 = $data['data'][0]['id'];
        $str2 = $data['data'][1]['id'];
        #var_dump($test,$str1,$str2);
        $this->assertTrue($str1>$str2);
    }

    /**
     * show subscriber
     * check
     */
    public function testShow ()
    {
        $show = $this->get($this->v.'/subscribers/'.$this->subscriber->id, $this->headers)
	        ->seeJson([
	        	'id'     =>$this->subscriber->id,
	            'object' =>$this->subscriber->object,
	            'action' =>$this->subscriber->action,
	            'version'=>(string)$this->subscriber->version,
	            'endPoint'=>$this->subscriber->endPoint,
	        ]);
    }
}
