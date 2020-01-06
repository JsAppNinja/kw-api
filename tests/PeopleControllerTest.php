<?php

class PeopleControllerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    /** @var  \App\ApiUser */
    protected $apiUser;

    protected function setUp()
    {
        parent::setUp();

        $this->apiUser = factory(App\ApiUser::class)->create([
            'isActive' => 1,
            'company' => 'Unit Test API',
            'application' => 'unit-test-api.com',
        ]);
    }

    public function testLookUpByPhone()
    {
        $url = '/v1/people/social/lookupPhone?phone=+13037170414';
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])->assertResponseOk();
    }

    public function testLookUpByPhoneAndCountry()
    {
        $url = '/v1/people/social/lookupPhone?phone=+13037170414&countryCode=US';
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])->assertResponseOk();
    }

    public function testLookupByEmail()
    {
        $url = '/v1/people/social/lookupEmail?email=vollossy@gmail.com';
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])
            ->assertResponseOk();
    }

    /**
     * Tests queries which are supposed to be long-running and should notify about it code and at last returns 404 code
     */
    public function testLookUpByEmailLongRunningQuery()
    {


        //TODO figure out why this test is failing....
        $this->markTestSkipped();



        $email = md5(date('Y-m-d H:i:s')) . '@wghx.com';
        $url = '/v1/people/social/lookupEmail?email=' . $email;
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])
            ->assertResponseStatus(200)
            ->seeJson([
                'status' => 202
            ]);

        sleep(2);

        $this->get($url, ['apiKey' => $this->apiUser->apiKey])
            ->assertResponseStatus(404);
    }

    private function setupMockForPersonService($method, $param){
        $peopleServiceMocke = $this->getMockBuilder(\App\Services\PersonService::class)->disableOriginalConstructor()
            ->getMock();
        $peopleServiceMocke
            ->expects($this->once())->method($method)->with($this->equalTo($param))
            ->willReturn(['status' => 200]);
        app()->singleton(\App\Services\PersonService::class, function () use ($peopleServiceMocke) {
            return $peopleServiceMocke;
        });
    }

    public function testSingleEntryPoint()
    {
        $email = 'vollossy@gmail.com';
        $this->setupMockForPersonService('findByEmail', $email);
        $url = '/v1/people/social/lookup?email='.$email;
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])
            ->assertResponseOk();

        $phone = '+12345678901';
        $this->setupMockForPersonService('findByPhone', $phone);
        $url = '/v1/people/social/lookup?phone='.$phone;
        $this->get($url, ['apiKey' => $this->apiUser->apiKey])
            ->assertResponseOk();
    }
}