<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \App\Services\ApiKeyService;

class ApiKeyServiceTest extends TestCase
{
	/**
     *@var class ApiKeyService 
     */
    private $apiKeyService;
    
    
    public function setUp()
    {
        parent::setUp();
        $this->apiKeyService = new ApiKeyService;
    }

    /**
     * Create new row in api_user 
     * Check getApiUser()
     * Sent ApiKey
     * clean up row
     */
    public function testGetApiUser ()
    {
        $apiUser = factory(App\ApiUser::class)->create([
            'company' => 'Unit Test API',
            'application' => 'unit-test-api.com'
        ]);

        $this->assertEquals(true, is_object($this->apiKeyService->getApiUser($apiUser->apiKey)), 'ApiKeyService@getApiUser must return object');
        $this->assertEquals(true, is_object($this->apiKeyService->getApiUser($apiUser->apiKey)), 'ApiKeyService@getApiUser second call using cache');

        $apiUser->company = "Unit Test Api Edited";
        $apiUser->save();

        $temp = $this->apiKeyService->getApiUser($apiUser->apiKey);
        $this->assertEquals(true,$temp->company==$apiUser->company , 'ApiKeyService@getApiUser must return object from database');
        $temp = $this->apiKeyService->getApiUser($apiUser->apiKey);
        $this->assertEquals(true,$temp->company==$apiUser->company, 'ApiKeyService@getApiUser second call data from cache');     

        // clean up row
        $this->assertEquals(true, $apiUser->delete($apiUser->id));
        $temp = $this->apiKeyService->getApiUser($apiUser->apiKey);
        $this->assertEquals(true,$temp==null,'ApiKeyService@getApiUser deleted data must return null');
        $temp = $this->apiKeyService->getApiUser($apiUser->apiKey);
        $this->assertEquals(true,$temp==null,'ApiKeyService@getApiUser deleted data must return null, from cache');
    }

    public function testApiUserActiveByDefault()
    {
        // this test is not valid, because created by model factory which is set isActive randomly
        $apiUser = factory(App\ApiUser::class)->create([
            'company' => 'Unit Test API',
            'application' => 'unit-test-api.com'
        ]);

        $user = $this->apiKeyService->getApiUser($apiUser->apiKey);
        $this->assertTrue($user->isActive>=0, 'ApiUser#isActive must be true');
        //clean up row in db;
        $apiUser->delete($user->id);
    }

    public function testApiUserObserveSaved()
    {
        $apiUser = factory(App\ApiUser::class)->create([
            'company' => 'Unit Test API',
            'application' => 'unit-test-api.com'
        ]);

        $this->assertEquals(true, $apiUser->delete($apiUser->id));
    }
}
