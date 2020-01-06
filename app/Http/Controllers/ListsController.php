<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateListRequest;
use App\Http\Requests\AssignListAgentRequest;
use App\RoutingList;
use App\Services\ListService;
use App\Services\ApiKeyService;

use App\Contracts\ListServiceInterface;

class ListsController extends Controller
{

  protected $listService;

  public function __construct(ListServiceInterface $listService) {
    $this->listService = $listService;
  }

  //
  public function store(CreateListRequest $request)
  {
    $apiuser = ApiKeyService::getApiUser($request->header('apiKey'));
    $data = $request->only(['name','router','hash']);
    $data["api_user_id"] = $apiuser->id;

    $list = $this->listService->createList($data);
    return response()->json($list);
  }

  public function index(Request $request)
  {
    if($request->header('apiKey')) {
      $apiuser = ApiKeyService::getApiUser($request->header('apiKey'));
      $lists = ListService::lists(array('api_user_id' => $apiuser->id));
      return response()->json($lists);
    } else {
      $lists = ListService::paginate(10);
      return response()->json($lists);
    }
  }

  /**
   * Show detail of an list
   * @param RoutingList $routingList
   * @return Response
   */
  public function show(RoutingList $routingList)
  {
    return response()->json($routingList);
  }

  public function showStats(RoutingList $routingList)
  {
    $stats = $this->listService->getStats();
    return response()->json($stats);
  }

  public function assignAgent(AssignListAgentRequest $request) {
    $data = $request->only(['lead_info']);
    $lead = $this->listService->assignAgent($data['lead_info']);

    return response()->json($lead);
  }
}
