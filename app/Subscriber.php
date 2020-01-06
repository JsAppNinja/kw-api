<?php
/**
 * User: joshteam
 * Date: 6/24/16
 * Time: 11:20 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObserveCacheTrait;
use App\Event;

class Subscriber extends Model
{

    use ObserveCacheTrait;

    /**
     * this used for ObserveCacheTrait, for managing model cached creating and desctuction by keys.
     * @var string
     */
    public $modelCachedKeys = 'object,action,version';
    public $modelPrefix = Event::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['object','action','version','endPoint','event_id','api_user_id'];

    /**
     * Relation belong to ApiUser
     */
    public function apiUser()
    {
        return $this->belongsTo('App\ApiUser','api_user_id');
    }

    /**
     * Relation belong to event
     */
    public function event()
    {
    	return $this->belongsTo('App\Event');
    }
}
