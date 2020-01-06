<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PointOfInterestService;
use App\Http\Requests\PointOfInterestRequest;
use App\Http\Requests;
use App\Jobs\StorePlacesResponse;

class PointOfInterestController extends Controller
{
    /**
     * @param PointOfInterestRequest $request
     * @return mixed
     */
    public function getPlaces(PointOfInterestRequest $request)
    {
        $searchParams = [
            'address'   => $request->input('address'),
        ];

        $result = app(PointOfInterestService::class)->getGeoCode($searchParams);

        if (count($result['results']) > 0)
        {
            $geoCode = $result['results'][0]['geometry']['location'];

            $searchParams = [
                'location'   => $geoCode['lat'].', '.$geoCode['lng'],
                'radius'     => 500,
                'keyword'    => $request->input('keyword'),
                'type'       => $request->input('type'),
            ];

            $locations = app(PointOfInterestService::class)->getLocations($searchParams);
            //save the results to db
            $this->dispatch(new StorePlacesResponse($locations['results']));

            return response()->json($locations);
        }

        return response()->json(['message'=>'Not a valid address']);

    }
}