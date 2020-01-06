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
 * Class PointOfInterestService
 * @package App\Services
 * This class provides functionality for retrieving places information from Google api
 */
class PointOfInterestService
{
    /**
     * Endpoint url for Google places api
     */
    const POI_API_URL = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

    /**
     * End point url for Google geocoding api
     */
    const GEOCODING_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var HandlerStack
     */
    private $_handler;

    /**
     * PointOfInterestService constructor.
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
     * @param Uri $uri Uri for request to Google api(@see self::getGeoCode())
     * @param array $params paramters for request to Google api(@see self::getGeoCode())
     * @return array Decoded response from Google
     * @throws PointOfInterestRequestException Thrown when there is error in request to Google api
     * or in Google api response
     * (for example, when there is server error)
     * @throws UnsupportedPointOfInterestResponseRecievedException Thrown when server responded with
     * unsupported error
     */
    private function executeRequest($uri,$params)
    {
        $googleSettings = ['key'=>config('services.google.key')];
        $params = http_build_query($params + $googleSettings);

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
                $this->reportError("Error with Google server", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupported error returned from Google ", $e->getCode(),
                $e->getMessage(), UnsupportedPointOfInterestResponseRecievedException::class);
        } catch (ClientException $e) {
            if (in_array($e->getCode(), [400, 403, 404, 405, 422])) {
                $this->reportError("Error in request to Google api.", $e->getCode(), $e->getMessage());
            }
            $this->reportError("Unsupport client error in request to Google api.", $e->getCode(),
                $e->getMessage(), UnsupportedPointOfInterestResponseRecievedException::class);

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
        $this->reportError("Unsupported positive response from Google API server", $response->getStatusCode(), $body,
            UnsupportedPointOfInterestResponseRecievedException::class);
    }

    public function getLocations($searchParams)
    {
        $params = $searchParams;
        return $this->executeRequest(new Uri(self::POI_API_URL),$params);
    }

    public function getGeoCode($address)
    {
        return $this->executeRequest(new Uri(self::GEOCODING_API_URL),$address);
    }

    /**
     * Reports error, received from Google api. It's just a helper function
     * @param string $message Message for exception and logging error
     * @param int $code Response code from datafinder api
     * @param string $body datafinder api response body
     * @param $exceptionClass
     */
    private function reportError($message, $code, $body, $exceptionClass = PointOfInterestRequestException::class)
    {
        \Log::error($message, [
            'code' => $code,
            'response' => $body
        ]);
        throw new $exceptionClass($code, $body);
    }
}

/**
 * Class PointOfInterestRequestException
 * Supposed to be used in PointOfInterestService to notify about errors from Google api
 * @package App\Services
 */
class PointOfInterestRequestException extends HttpException
{
}

class UnsupportedPointOfInterestResponseRecievedException extends HttpException
{
}