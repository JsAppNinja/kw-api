<?php
namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class PersonService
 * @package App\Services
 * This class provides functionality for retrieving person information from fullcontact api
 */
class PersonService
{
    /**
     * Endpoint url for person api
     */
    const API_URL = 'https://api.fullcontact.com/v2/person.json';

    /**
     * Header with API key that should be placed in each request
     */
    const AUTH_HEADER = 'X-FullContact-APIKey';

    /**
     * @var String Api key for application
     */
    private $_apiKey;

    /**
     * @var HandlerStack
     */
    private $_handler;

    /**
     * PersonService constructor.
     * @param string $apiKey Api key for fullcontact(it is supposed to use FULLCONTACT_API_KEY environment variable)
     * @param null|HandlerStack $handler Handler to perform requests. Main purpose is testing.
     */
    public function __construct($apiKey, HandlerStack $handler = null)
    {
        $this->_apiKey = $apiKey;
        $this->_handler = HandlerStack::create();
        if (!empty($handler)) {
            $this->_handler = $handler;
        }
    }

    /**
     * Actually executes request to api, using provided uri. This method caches results for successful
     * requests(requests which are returned with 200 status code)
     * @param Uri $uri Uri for request to fullcontact api(@see self::findPerson())
     * @return array Decoded response from fullcontact
     * @throws PersonRequestException Thrown when there is error in request to fullcontact or in fullcontact response
     * (for example, when there is server error)
     * @throws UnsupportedPersonResponseRecievedException Thrown when server responded with unsupported error
     */
    private function executeRequest($uri)
    {
        $result = \Cache::get($uri->getQuery());
        if ($result !== null) {
            return $result;
        }
        $client = new Client(['handler' => $this->_handler]);
        $request = new Request('GET', $uri, [self::AUTH_HEADER => $this->_apiKey]);

        try {
            $response = $client->send($request);
        } catch (ServerException $e) {
            if (in_array($e->getCode(), [500, 503])) {
                $this->reportError("Error with fullcontact server", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupported error returned from fullcontactServer", $e->getCode(),
                $e->getMessage(), UnsupportedPersonResponseRecievedException::class);
        } catch (ClientException $e) {
            if (in_array($e->getCode(), [400, 403, 404, 405, 422])) {
                $this->reportError("Error in request to fullcontact api.", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupport client error in request to fullcontact api.", $e->getCode(),
                $e->getMessage(), UnsupportedPersonResponseRecievedException::class);

        }

        $body = $response->getBody()->getContents();
        /**
         * if everything ok with request, return its body(including if that request still processing)
         */
        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 202) {
            $result = json_decode($body, true);
            if ($response->getStatusCode() == 200) {
                \Cache::put($uri->getQuery(), $result);
            }
            return $result;
        }
        $this->reportError("Unsupported positive response from fullcontact server", $response->getStatusCode(), $body,
            UnsupportedPersonResponseRecievedException::class);
    }

    private function findPerson($key, $value)
    {
        $uri = Uri::withQueryValue(new Uri(self::API_URL), $key, $value);
        return $this->executeRequest($uri);
    }

    /**
     * @param string $email Email to search requests
     * @return array Hash table of deserialized response of
     * fullcontact({@link https://www.fullcontact.com/developer/docs/person/#lookup-by-email })
     * @throws PersonRequestException
     */
    public function findByEmail($email)
    {
        return $this->findPerson('email', $email);
    }

    public function findByPhone($phone, $countryCode = null)
    {
        if (empty($countryCode)) {
            return $this->findPerson('phone', $phone);
        }
        $uri = Uri::withQueryValue(new Uri(self::API_URL), 'phone', $phone);
        $uri = Uri::withQueryValue($uri, 'countryCode', $countryCode);
        return $this->executeRequest($uri);
    }

    /**
     * Reports error, received from fullcontact api. It's just a helper function
     * @param string $message Message for exception and logging error
     * @param int $code Response code from fullcontact api
     * @param string $body Fullcontact api response body
     * @param $exceptionClass
     */
    private function reportError($message, $code, $body, $exceptionClass = PersonRequestException::class)
    {
        \Log::error($message, [
            'code' => $code,
            'response' => $body
        ]);
        throw new $exceptionClass($code, $body);
    }
}

/**
 * Class PersonRequestException
 * Supposed to be used in PersonService to notify about errors from fullcontact api
 * @package App\Services
 * todo: may be that exception with PersonService should be moved to subpackage
 */
class PersonRequestException extends HttpException
{
}

class UnsupportedPersonResponseRecievedException extends HttpException
{
}