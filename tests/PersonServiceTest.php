<?php
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use App\Services\PersonService;

/**
 * Class PersonServiceTest
 * This class for testing PersonService, all requests are made via MockHandler, so it doesn't recieve actual data
 */
class PersonServiceTest extends TestCase
{
    const RESPONSE_FILE_NAME = 'tests/find_by_email_response.txt';
    const EMAIL = 'bart@fullcontact.com';
    const API_KEY = 'none';
    const PHONE = '+13037170414';
    const COUNTRY = 'US';
    
    const METHOD_FIND_BY_EMAIL = 'findByEmail';
    const METHOD_FIND_BY_PHONE = 'findByPhone';

    /**
     * Provides actual testing for testFindByEmailOk and testFindByPhoneOk methods. This method suppose that request
     * is correct and will return 200
     * @param string $method Method name to be called
     * @param mixed $param Parameter for that method(must be supported by method)
     */
    private function findOk($method, $param)
    {
        $fileResource = fopen(storage_path(self::RESPONSE_FILE_NAME), 'r');
        $mockHandler = new \GuzzleHttp\Handler\MockHandler([
            new Response(200, ['Content-Type' => 'application/json;charset=UTF-8'], $fileResource)
        ]);
        $handlerStack = HandlerStack::create($mockHandler);

        $service = new \App\Services\PersonService(self::API_KEY, $handlerStack);

        $result = $service->$method($param);
        $this->assertNotNull($result);
        $this->assertEquals($result['status'], 200);
        $this->assertEquals($result['contactInfo']['fullName'], "Bart Lorang");
    }

    /**
     * Tests PersonService::findByEmail() method. Supposed that everything is okay(response from api is 200)
     */
    public function testFindByEmailOk()
    {
        $this->findOk(self::METHOD_FIND_BY_EMAIL, self::EMAIL);
    }

    /**
     * Tests PersonService::findByPhone() method. Supposed that everything is okay(response from api is 200)
     */
    public function testFindByPhoneOk()
    {
        $this->findOk(self::METHOD_FIND_BY_PHONE, self::PHONE);
    }

    /**
     * Return PersonService instance with mock handler setup for errors
     * @param integer $errorCode Error code that should be thrown by api stub
     * @return \App\Services\PersonService
     */
    private function getServiceForError($errorCode)
    {
        $mockHandler = new \GuzzleHttp\Handler\MockHandler([
            new Response($errorCode)
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        return new \App\Services\PersonService(self::API_KEY, $handlerStack);
    }

    /**
     * Executes method on service and expects exception(\App\Services\PersonRequestException) from it
     * @param string $method Method to execute
     * @param string $param Method's param
     * @param int $errorCode Error code, which will be thrown(@see self::getServiceForError())
     */
    private function findWithError($method, $param, $errorCode){
        $service = $this->getServiceForError($errorCode);
        $this->setExpectedException(\App\Services\PersonRequestException::class);
        $service->$method($param);
    }


    public function testFindByEmailFullcontactServerError500()
    {
        $this->findWithError(self::METHOD_FIND_BY_EMAIL, self::EMAIL, 500);
    }

    public function testFindByEmailFullcontactServerError503()
    {
        $this->findWithError(self::METHOD_FIND_BY_PHONE, self::PHONE, 503);
    }

    private function findWithErrors4xx($method, $param)
    {
        $errorCodes = [400, 403, 405, 422];
        foreach ($errorCodes as $code) {
            $service = $this->getServiceForError($code);
            try {
                $service->$method($param);
            } catch (\App\Services\PersonRequestException $e) {
                $this->assertTrue(in_array($e->getStatusCode(), $errorCodes));
            }
        }
    }
    
    public function testFindByEmailErrors4xx()
    {
        $this->findWithErrors4xx('findByEmail', self::EMAIL);
    }

    public function testByPhoneErrors4xx()
    {
        $this->findWithErrors4xx('findByEmail', self::PHONE);
    }

    private function findWithUnsupportedResponseErrors($method, $param)
    {
        $statusCodes = [401, 504, 204];
        foreach ($statusCodes as $statusCode) {
            $service = $this->getServiceForError($statusCode);
            try{
                $service->$method($param);
            }catch (\App\Services\UnsupportedPersonResponseRecievedException $e){
                $this->assertTrue(in_array($e->getStatusCode() ,  $statusCodes));
            }
        }
    }

    public function testFindByEmailUnsupportedResponse()
    {
        $this->findWithUnsupportedResponseErrors(self::METHOD_FIND_BY_EMAIL, self::EMAIL);
    }

    public function testFindByPhoneUnsupportedResponse()
    {
        $this->findWithUnsupportedResponseErrors(self::METHOD_FIND_BY_PHONE, self::PHONE);
    }

    /**
     * Tests people lookup by providing phone number and countryCode
     */
    public function testFindByPhoneAndCountry()
    {
        $container = [];
        $history = \GuzzleHttp\Middleware::history($container);
        $fileResource = fopen(storage_path(self::RESPONSE_FILE_NAME), 'r');
        $mockHandler = new \GuzzleHttp\Handler\MockHandler([
            new Response(200, ['Content-Type' => 'application/json;charset=UTF-8'], $fileResource)
        ]);
        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push($history);

        $service = new \App\Services\PersonService(self::API_KEY, $handlerStack);

        $result = $service->findByPhone(self::PHONE, self::COUNTRY);
        $this->assertNotNull($result);
        $this->assertEquals($result['status'], 200);
        $this->assertEquals($result['contactInfo']['fullName'], "Bart Lorang");
        
        $this->assertCount(1, $container);

        $expectedUri = new Uri(PersonService::API_URL);
        $expectedUri = Uri::withQueryValue($expectedUri, 'phone', self::PHONE);
        $expectedUri = Uri::withQueryValue($expectedUri, 'countryCode', self::COUNTRY);
        /** @var \GuzzleHttp\Psr7\Request $requestMade */
        $requestMade = $container[0]['request'];
        $this->assertEquals($expectedUri->getQuery(), $requestMade->getUri()->getQuery());
    }
}