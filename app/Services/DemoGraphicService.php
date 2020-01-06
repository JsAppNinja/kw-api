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
 * Class DemoGraphicService
 * @package App\Services
 * This class provides functionality for retrieving person information from demographic api
 */
class DemoGraphicService
{
    /**
     * Endpoint url for person api
     */
    const API_URL = 'http://api.datafinder.com/qdf.php';

    /**
     * @var HandlerStack
     */
    private $_handler;

    /**
     * DemoGraphicService constructor.
     * @param null|HandlerStack $handler Handler to perform requests. Main purpose is testing.
     */
    public function __construct(HandlerStack $handler = null)
    {
        $this->_handler = HandlerStack::create();
        if (!empty($handler) && strlen($handler)>0) {
            $this->_handler = $handler;
        }
    }

    /**
     * Actually executes request to api, using provided uri. This method caches results for successful
     * requests(requests which are returned with 200 status code)
     * @param Uri $uri Uri for request to datafinder api(@see self::findDemoGraphic())
     * @param array $params paramters for request to datafinder api(@see self::findDemoGraphic())
     * @return array Decoded response from datafinder
     * @throws DemoGraphicRequestException Thrown when there is error in request to datafinder or in datafinder response
     * (for example, when there is server error)
     * @throws UnsupportedDemoGraphicResponseRecievedException Thrown when server responded with unsupported error
     */
    private function executeRequest($uri,$params)
    {
        $pathFinderSettings = ['service'=>config('services.datafinder.service'),
                               'k2'=>config('services.datafinder.key')];
        $params = http_build_query($params + $pathFinderSettings);

        $result = \Cache::get($uri.'?'.$params);
        if ($result !== null) {
            return $result;
        }
        $client = new Client(['handler' => $this->_handler]);
        $request = new Request('GET', $uri.'?'.$params);

        try {
            $response = $client->send($request);
        } catch (ServerException $e) {
            if (in_array($e->getCode(), [500, 503])) {
                $this->reportError("Error with datafinder server", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupported error returned from datafinder", $e->getCode(),
                $e->getMessage(), UnsupportedPersonResponseRecievedException::class);
        } catch (ClientException $e) {
            if (in_array($e->getCode(), [400, 403, 404, 405, 422])) {
                $this->reportError("Error in request to datafinder api.", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupport client error in request to datafinder api.", $e->getCode(),
                $e->getMessage(), UnsupportedDemoGraphicResponseRecievedException::class);

        }

        $body = $response->getBody()->getContents();
        /**
         * if everything ok with request, return its body(including if that request still processing)
         */
        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 202) {
            $result = json_decode($body, true);
           if ($response->getStatusCode() == 200) {
                \Cache::forever($uri.'?'.$params, $result);
            }
            return $result;
        }
        $this->reportError("Unsupported positive response from datafinder server", $response->getStatusCode(), $body,
            UnsupportedDemoGraphicResponseRecievedException::class);
    }

    public function findDemoGraphic($searchParams)
    {
        $params = $searchParams;
        return $this->executeRequest(new Uri(self::API_URL),$params);
    }

    /**
     * Reports error, received from datafinder api. It's just a helper function
     * @param string $message Message for exception and logging error
     * @param int $code Response code from datafinder api
     * @param string $body datafinder api response body
     * @param $exceptionClass
     */
    private function reportError($message, $code, $body, $exceptionClass = DemoGraphicRequestException::class)
    {
        \Log::error($message, [
            'code' => $code,
            'response' => $body
        ]);
        throw new $exceptionClass($code, $body);
    }
}

/**
 * Class DemoGraphicRequestException
 * Supposed to be used in PersonService to notify about errors from datafinder api
 * @package App\Services
 * todo: may be that exception with PersonService should be moved to subpackage
 */
class DemoGraphicRequestException extends HttpException
{
}

class UnsupportedDemoGraphicResponseRecievedException extends HttpException
{
}