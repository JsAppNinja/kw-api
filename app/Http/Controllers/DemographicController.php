<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DemoGraphicService;
use App\Http\Requests;
use App\Http\Requests\DemographicRequest;
use App\Jobs\StorePersonResponse;

class DemographicController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getDemographics(DemographicRequest $request)
    {

        $searchParams = [
            'd_first'   => $request->input('d_first'),
            'd_last'    => $request->input('d_last'),
            'd_zip'     => $request->input('d_zip'),
            'd_fulladdr'=> $request->input('d_fulladdr'),
            'd_city'    => $request->input('d_city'),
            'd_state'   => $request->input('d_state'),
            'd_phone'   => $request->input('d_last'),
            'd_email'   => $request->input('d_email'),
            'd_lat'     => $request->input('d_lat'),
            'd_long'    => $request->input('d_long'),
            'd_ip'      => $request->input('d_ip'),
        ];

        $result = app(DemoGraphicService::class)->findDemoGraphic($searchParams);

        if (isset($result['datafinder']['results']))
        {
            $this->dispatch(new StorePersonResponse($result['datafinder']['results']));
        }
        return response()->json($result);

    }
}
