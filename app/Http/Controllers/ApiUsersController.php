<?php

namespace App\Http\Controllers;

use App\ApiUser;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ApiUserRequest;
use App\Http\Requests\FilterPageRequest;

class ApiUsersController extends Controller
{
    
	/**
     * @SWG\Get(
     *     path="/api_users",
     *     tags={"apiUsers"},
     *     operationId="list api_user",
     *     summary="list of api users",
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
     * list all api users paginated 
     *
     * @param App\Http\Requests\FilterPageRequest $request
     * @return Response
     */
    public function index(FilterPageRequest $request)
    {
        $perpage = $request->getPerPage();
        $sortdir = $request->getSortDir();
        $sortfield = $request->getSortField();
        $filters = $request->getFilters();

        $apiUsers = new ApiUser;

        if ($filters) {
            foreach ($filters as $key=>$val) {
                if ($key=="company" || $key=="apiKey") {
                    $apiUsers = $apiUsers->where($key,"like","%".trim($val)."%");
                } else {
                    $apiUsers = $apiUsers->where($key,trim($val));
                }
            }
        }
        $apiUsers = $apiUsers->orderBy($sortfield,$sortdir);
        
        return response()->json($apiUsers->paginate($perpage));
    }

    /**
     * @SWG\Post(
     *     path="/api_users",
     *     tags={"apiUsers"},
     *     operationId="create api_user",
     *     summary="Create api user",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="apiKey",
     *         in="formData",
     *         description="apiKey of api user",
     *         required=true,
     *         type="string",
     *         default="1234567890abcdef",
     *     ),
     *     @SWG\Parameter(
     *         name="company",
     *         in="formData",
     *         description="company user of api key",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="application",
     *         in="formData",
     *         description="application name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="email for notification",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     * )
     */
    /**
     * create new api user data
     *
     * @return Response
     */
    public function store(ApiUserRequest $request)
    {
    	$data = $request->only(["apiKey","company","application","email"]);
    	$data["isActive"] = 1;

    	$apiUser = ApiUser::create($data);

    	return response()->json($apiUser);
    }

    /**
     * @SWG\Get(
     *     path="/api_users/{id}",
     *     tags={"apiUsers"},
     *     operationId="get api_user",
     *     summary="show detail of api user",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of api user",
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
    	$apiUser = ApiUser::find($id);
    	if (!$apiUser) {
    		throw new \Exception("Not Found", 404);
    	}

    	return response()->json($apiUser);
    }

    /**
     * @SWG\Put(
     *     path="/api_users/{id}",
     *     tags={"apiUsers"},
     *     operationId="update api_user",
     *     summary="Update api user data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of api user",
     *         required=true,
     *         type="integer",
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         name="apiKey",
     *         in="formData",
     *         description="apiKey of api user",
     *         required=true,
     *         type="string",
     *         default="1234567890abcdef",
     *     ),
     *     @SWG\Parameter(
     *         name="company",
     *         in="formData",
     *         description="company user of api key",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="application",
     *         in="formData",
     *         description="application name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="email for notification",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     * )
     */
    /**
     * update api user data
     *
     * @return Response
     */
    public function update($id, ApiUserRequest $request)
    {
    	$data = $request->only(["apiKey","company","application","email"]);

    	$apiUser = ApiUser::find($id);
    	if (!$apiUser) {
    		throw new \Exception("Not Found", 404);
    	}

    	$apiUser->update($data);

    	return response()->json($apiUser);

    }

    /**
     * @SWG\Get(
     *     path="/api_users/{id}/toggle",
     *     tags={"apiUsers"},
     *     operationId="toggle api_user",
     *     summary="Toggle active status of api user",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of api user",
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
     * toggle api user active status
     *
     * @return Response
     */
    public function toggle($id)
    {
    	$apiUser = ApiUser::find($id);
    	if (!$apiUser) {
    		throw new \Exception("Not Found", 404);
    	}
    	$apiUser->toggleActive();

    	return response()->json($apiUser);
    }

    /**
     * @SWG\Delete(
     *     path="/api_users/{id}",
     *     tags={"apiUsers"},
     *     operationId="delete api_user",
     *     summary="Delete api user",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of api user",
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
     * delete api user data
     *
     * @return Response
     */
    public function destroy($id, Request $request)
    {
    	$apiUser = ApiUser::find($id);

        if (!policy($apiUser)->delete($request,$apiUser)) {
            abort(403,"Unauthorized for delete Api User");   
        }

        $apiUser->delete();

        return response()->json($apiUser);
    }

    /**
     * @SWG\Get(
     *     path="/api_users/{apiKey}/check",
     *     tags={"apiUsers"},
     *     operationId="check key api_user",
     *     summary="check apiKey api user",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="apiKey",
     *         in="path",
     *         description="apiKey of api user",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Bad Request",
     *     ),
     * )
     */
    public function check($apiKey)
    {
        $apiUser = ApiUser::where("apiKey",$apiKey)
            ->where("isActive",1)->first();
        if (!$apiUser) {
            abort(404,"Api User Not Found or Not Valid!");
        }

        return response()->json($apiUser);
    }
}
