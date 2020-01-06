<?php
/**
 * User: joshteam
 * Date: 6/24/16
 * Time: 11:20 PM
 */

namespace App\Services\LeadRouting;

use App\Contracts\LeadRouterServiceInterface;
use App\ListAgent;
use App\ListLead;
use DB;

use Log;

class RoundRobinLeadRouterService implements LeadRouterServiceInterface
{
  /**
   * apply
   * @param  Integer $list_id
   * @param  Array $filter
   * @return ListAgent agent model selected via round robin routing algorithm
   */
  public function apply($list_id, $filter) {
    $agents = ListAgent::where('list_id',$list_id)->where('leads_given', 0)->orderBy('last_lead_received', 'asc')->get();

    if (count($agents) == 0) {
      return false;
    }

    return $agents[0];
  }
}