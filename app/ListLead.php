<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListLead extends Model
{
  protected $fillable = ['list_id','list_agent_id','router'];

  /**
   * Relation belong to list
   */
  public function list()
  {
    return $this->belongsTo('App\RoutingList','list_id');
  }


  /**
   * Relation belong to listAgents
   */
  public function listAgent()
  {
    return $this->belongsTo('App\ListAgent','list_agent_id');
  }
}
