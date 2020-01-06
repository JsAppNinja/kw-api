<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListAgent extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['list_id','agent_id','name'];

  /**
   * Relation belong to list
   */
  public function list()
  {
    return $this->belongsTo('App\RoutingList','list_id');
  }
}
