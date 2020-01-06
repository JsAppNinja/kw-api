<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ApiUser;

class ApiUsersControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * data array
     * @var array
     */
    private $data;
    
    /**
     * Create row in api_user
     * @var object
     */
    private $apiUser;

    /**
     * headers data for request
     * @var array
     */
    private $headers;
    
    public function setUp()
    {
        parent::setUp();
        $this->data = ['apiKey'=>md5(str_random(20)),
                       'company'=>'UnitTest_'.str_random(5),
                       'application' => 'Unit-tes.com',
                       'email'=>'email@company.com',
                      ];
        $this->apiUser = factory(App\ApiUser::class)->create(['isActive'=>'1']);
        $this->headers = ['apiKey'=>$this->apiUser->apiKey];
    }
    
    public function testIndex()
    {
        $test = $this->get($this->v.'/api_users/',$this->headers)->assertResponseOk();
        $content = json_decode($this->response->getContent(),true);

        $this->assertTrue($content['total']>0);
    }
    
    public function testStore()
    {  
        $test = $this->post($this->v.'/api_users/', $this->data, $this->headers)->seeJson($this->data)->assertResponseOk();
    }
    
    public function testShow()
    {
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company;
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;
        
        $test = $this->get($this->v.'/api_users/'.$this->apiUser->id, $this->headers)
                    ->seeJson($this->data)
                    ->assertResponseOk();
    }
    
    public function testShowFailed()
    {
        $test = $this->get($this->v.'/api_users/xc46',$this->headers)->assertResponseStatus(404);
    }
    
    public function testUpdate()
    {
        $this->data['id']           = $this->apiUser->id;
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company."unit_test";
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;  
        
        $test = $this->put($this->v.'/api_users/'.$this->data['id'],$this->data, $this->headers)
                     ->seeJson($this->data)
                     ->assertResponseOk();
    }
    
    public function testToggle()
    {
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company;
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;
        
        $test = $this->get($this->v.'/api_users/'.$this->apiUser->id.'/toggle', $this->headers)
                     ->seeJson($this->data)
                     ->assertResponseOk();
    }
    
    public function testApiUserDisableIsActive()
    {
         $ApiUser = ApiUser::find($this->apiUser->id);
         $this->assertTrue($ApiUser->disable());
    }
    
    public function testApiUserDisableIsActiveFailed()
    {
        //$this->apiUser = null;
        $ApiUser = ApiUser::find('xu8sn');
        $this->assertNull($ApiUser);
    }
    
    public function testApiUserEnableIsActive()
    {
       $ApiUser = ApiUser::find($this->apiUser->id);
       $this->assertTrue($ApiUser->enable()); 
    }
    
    public function testApiUserEnableIsActiveFailed()
    {
       //$this->apiUser = null;
       $ApiUser = ApiUser::find('xu8sn');
       $this->assertNull($ApiUser);
    }

    /**
     * test ApiUsersController@index with filtering option
     * 
     */
    public function testListWithFilter()
    {
        $this->get($this->v.'/api_users/'.$this->apiUser->id, $this->headers);
        $json = $this->response->getContent();
        $data = json_decode($json,true);

        $test = $this->json('GET',$this->v.'/api_users/',['_filters'=>'{"apiKey":"'.$this->apiUser->apiKey.'"}'], $this->headers);
        $this->seeJson(['total'=>1,'data'=>[$data]])->assertResponseOk();
    }

    /**
     * test ApiUsersController@index with pagination option
     * 
     */
    public function testListWithPage()
    {
        $test = $this->json('GET',$this->v.'/api_users/',['_perPage'=>5], $this->headers);
        //var_dump($this->response->getContent());
        $this->seeJson(['per_page'=>5])->assertResponseOk();   
    }

    /**
     * test ApiUsersController@index with sorting option
     * 
     */
    public function testListWithSortField()
    {
        $this->json('GET',$this->v.'/api_users/',['_perPage'=>5,'_sortDir'=>'desc','_sortField'=>'company'], $this->headers);
        $test = $this->response->getContent();

        $data = json_decode($test,true);
        $str1 = $data['data'][0]['company'];
        $str2 = $data['data'][1]['company'];
        #var_dump($test,$str1,$str2);
        $this->assertTrue($str1>$str2);
    }

    public function testCheck()
    {
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company;
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;

        // no need apiKey in header
        $this->get($this->v.'/api_users/'.$this->apiUser->apiKey.'/check');
        $this->seeJson($this->data)
            ->assertResponseOk();
    }

    // last thing
    public function testDestroyNoKeyFailed()
    {
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company;
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;

        $test = $this->delete($this->v.'/api_users/'.$this->apiUser->id,['id'=>$this->apiUser->id]);
        $this->assertResponseStatus(401);
    }

    public function testDestroy()
    {
        $this->data['apiKey']       = $this->apiUser->apiKey;
        $this->data['company']      = $this->apiUser->company;
        $this->data['application']  = $this->apiUser->application;
        $this->data['email']        = $this->apiUser->email;

        $test = $this->delete($this->v.'/api_users/'.$this->apiUser->id,['id'=>$this->apiUser->id], $this->headers);
        $this->seeJson($this->data)
            ->assertResponseOk();
    }
}