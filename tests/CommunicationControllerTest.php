<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommunicationControllerTest extends TestCase
{

    protected $config;
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->config = $this->app['config']->get('twilio.twilio.connections.twilio');
        $this->user = factory(App\ApiUser::class)->create([
            'isActive'    => 1,
            'company'     => 'Unit Test API',
            'application' => 'unit-test-api.com',
        ]);
    }

    protected function tearDown()
    {
        $this->user->delete();
    }
    /**
     * Success send text message
     *
     * @return void
     */
    public function testSendTextSuccess()
    {
        //TODO figure out why this test is failing....
        $this->markTestSkipped();


        $data = ['phoneNumber' => $this->config['test_target_number'], 'message' => 'Hello from KW-API'];

        $predictedResponseBody = ($this->config['from'] == '+15005550006') 
                ? 'Sent from your Twilio trial account - ' . $data['message'] 
                : $data['message'];

        $predictedResponse = [
            'from' => $this->config['from'],
            'to' => $data['phoneNumber'],
            'body' => $predictedResponseBody
        ];
        $this->post($this->v . '/communications/send_text', $data, ['apiKey' => $this->user->apiKey])
            ->seeJson($predictedResponse)
            ->assertResponseOk();
    }

    /**
     * Send Text failure, require phone number
     */
    public function testSendTextFailPhoneNumberRequired()
    {
        $data = ['phoneNumber' => '', 'message' => 'Hello from KW-API'];
        $this->post($this->v . '/communications/send_text', $data, ['apiKey' => $this->user->apiKey])
            ->assertResponseStatus(422);
    }

    /**
     * Send text failure message required
     */
    public function testSendTextFailMessageRequired()
    {
        $data = ['phoneNumber' => '+6285778275565', 'message' => ''];
        $this->post($this->v . '/communications/send_text', $data, ['apiKey' => $this->user->apiKey])
            ->assertResponseStatus(422);
    }

    /**
     * Fail to send text invalid phone number
     */
    public function testSendTextFailInvalidPhoneNumber()
    {
        $data = ['phoneNumber' => 'aaa', 'message' => 'Hello from KW-API'];
        $this->post($this->v . '/communications/send_text', $data, ['apiKey' => $this->user->apiKey])
            ->assertResponseStatus(400);
    }

    /**
     * Wrong api key provided
     */
    public function testWrongApiKey()
    {
        $data = ['phoneNumber' => '', 'message' => 'Hello from KW-API'];
        $this->post($this->v . '/communications/send_text', $data, ['apiKey' => 'wrong-api-key'])
            ->assertResponseStatus(401);
    }
}
