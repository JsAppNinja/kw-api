<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddListAgentRequest;
use App\Http\Requests\AddMultipleListAgentRequest;

use App\ListAgent;
use App\RoutingList;
use App\Services\ListService;

use App\Contracts\ListServiceInterface;

class ListAgentsController extends Controller
{
  protected $listService;

  public function __construct(ListServiceInterface $listService) {
    $this->listService = $listService;
  }


  public function store(AddListAgentRequest $request) {
    $data = $request->only(['agent_id','name']);
    $agent = $this->listService->addAgent($data);
    return response()->json($agent);
  }

  public function bulkstore(AddMultipleListAgentRequest $request) {
    $data = $request->only(['agents']);
    $agents = $this->listService->addAgents($data['agents']);
    return response()->json($agents);
  }

  public function index(RoutingList $list)
  {
    $agents = $list->listAgents()->paginate(10);
    return response()->json($agents);
  }

  /**
   * Show detail of an listAgent
   * @param RoutingList $list
   * @param ListAgent $agent
   * @return Response
   */
  public function show(RoutingList $list, ListAgent $agent)
  {
    return response()->json($agent);
  }

  public function showStats(RoutingList $routingList, ListAgent $agent)
  {
    $stats = $this->listService->getStats($agent->id);
    return response()->json($stats);
  }

  /**
   * remove listAgent from list
   * @param RoutingList $list
   * @param ListAgent $agent
   * @return Response
   */
  public function destroy(RoutingList $list, ListAgent $agent)
  {
    $result = $this->listService->removeAgent($agent->agent_id);
    return response()->json(array('status' => $result));
  }
}
