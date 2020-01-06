<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Subscriber;
use App\Http\Requests\FilterPageRequest;

class SubscriberController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/subscribers",
     *     tags={"subscribers"},
     *     operationId="list subscribers",
     *     summary="list of subscribers",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="page data",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_filters",
     *         in="query",
     *         description="filters for data",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_perPage",
     *         in="query",
     *         description="number of items for every page",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_sortField",
     *         in="query",
     *         description="sorting items using specified field",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_sortDir",
     *         in="query",
     *         description="sort direction using _sortField",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     * )
     */
    /**
     * list all subscribers paginated 
     * @param App\Http\Requests\FilterPageRequest $request
     * @return Response
     */
    public function index(FilterPageRequest $request)
    {
        $perpage = $request->getPerPage();
        $sortdir = $request->getSortDir();
        $sortfield = $request->getSortField();
        $filters = $request->getFilters();

        $subscribers = new Subscriber;

        if ($filters) {
            $fields = $subscribers->getFillable();
            foreach ($filters as $key=>$val) {
                $subscribers = $subscribers->where($key,trim($val));
            }
        }
        $subscribers = $subscribers->orderBy($sortfield,$sortdir);
        
        return response()->json($subscribers->paginate($perpage));
    }

    /**
     * @SWG\Get(
     *     path="/subscribers/{id}",
     *     tags={"subscribers"},
     *     operationId="get subscriber",
     *     summary="show detail of subscriber",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of subscriber",
     *         required=true,
     *         type="integer",
     *         default="1",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     * )
     */
    /**
     * show detail of api user data
     *
     * @return Response
     */
    public function show($id)
    {
        $subscriber = Subscriber::find($id);
        if (!$subscriber) {
            throw new \Exception("Subscriber Not Found", 404);
        }

        return response()->json($subscriber);
    }
}
