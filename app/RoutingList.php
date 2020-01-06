<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutingList extends Model
{

  protected $table = 'lists';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['api_user_id','name','hash','router'];

  /**
   * Relation belong to apiUser
   */
  public function apiUser()
  {
      return $this->belongsTo('App\ApiUser');
  }

  /**
   * Relation has many listAgents
   */
  public function listAgents()
  {
    return $this->hasMany('App\ListAgent', 'list_id', 'id');
  }

  /**
   * Relation has many listLeads
   */
  public function listLeads()
  {
    return $this->hasMany('App\ListLead', 'list_id', 'id');
  }
}
