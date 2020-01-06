<?php
/**
 * User: joshteam
 * Date: 6/24/16
 * Time: 11:20 PM
 */

namespace App\Services;

use App\Contracts\ListServiceInterface;
use App\Services\LeadRouting\RoundRobinLeadRouterService;
use App\RoutingList;
use App\ListAgent;
use App\ListLead;
use Hash;
use DB;

use Log;

class ListService implements ListServiceInterface
{
  protected $listModel;
  protected $leadRouter;

  /**
   * Create a new Service instance.
   *
   * @return void
   */
  public function __construct(RoutingList $list)  {
    $this->listModel = $list;

    if ($this->listModel->router) {
      $this->initRouter();
    }
  }

  private function initRouter() {

    /**
     * To think about
     * Decouple of Router from List service
     */

    /**
     * To do
     *
     * Get Lead router based on list router
     */

    $this->leadRouter = new RoundRobinLeadRouterService();
  }

  public function createList($data) {

    $this->listModel->hash = Hash::make($data["hash"]);
    $this->listModel->api_user_id = $data["api_user_id"];
    $this->listModel->name = $data["name"];
    $this->listModel->router = $data["router"];
    $this->listModel->save();

    $this->initRouter();
    return $this->listModel;
  }

  public function addAgent($data) {

    $agent = ListAgent::where('agent_id', $data["agent_id"])->first();

    if ($agent) {
      throw new \Exception('Agent ID has already been occupied.');
    }

    $data["list_id"] = $this->listModel->id;
    $agent = ListAgent::create($data);

    return $agent;
  }

  public function addAgents($data) {
    $results = array();
    foreach($data as $key=>$value) {
      $results[] = $this->addAgent($value);
    }
    return $results;
  }

  public function removeAgent($agent_id) {
    return ListAgent::where('list_id', '=', $this->listModel->id)
      ->where('agent_id', '=', $agent_id)
      ->delete();
  }

  public function assignAgent($lead) {
    //apply to route logic
    $selected_agent = $this->leadRouter->apply($this->listModel->id, $lead);

    if ($selected_agent == false) {
      throw new \Exception('All agents are occupied.');
    }

    //Create List Lead
    $leadModel = new ListLead;
    $leadModel->list_id = $this->listModel->id;
    $leadModel->list_agent_id = $selected_agent->id;
    $leadModel->lead_info = $lead;
    $leadModel->router = $this->listModel->router;

    $leadModel->save();

    //update agent info
    $selected_agent->leads_given = true;
    $selected_agent->last_lead_received = DB::raw('NOW()');
    $selected_agent->save();

    return $leadModel;
  }

  public function getStats($filter) {

    if ($filter) {
      $agents = $this->listModel->listAgents()->where('id',$filter)->get();
      $leads = $this->listModel->listLeads()->where('list_agent_id',$filter)->get();
    } else {
      $agents = $this->listModel->listAgents();
      $leads = $this->listModel->listLeads();
    }

    // TO DO FETCH LOG FROM ACTIVITY LOG
    return array("agents" => $agents, "leads" => $leads);
  }

  ///////////////////
  //STATIC METHODS //
  ///////////////////

  public static function lists($filter) {
    $defaultFilter = array('id'  => 1, 'api_user_id'  => 2, 'router' => 3);
    $filterKeys = array_intersect_key($filter, $defaultFilter);

    $query = DB::table('lists');

    foreach ($filterKeys as $key => $value) {
      $query->where($key, '=', $filter[$key]);
    }

    $results = $query->get();
    return $results;
  }
}