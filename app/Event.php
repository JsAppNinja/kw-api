<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObserveCacheTrait;
use Cache;

class Event extends Model
{

    use ObserveCacheTrait;

    /**
     * this used for ObserveCacheTrait, for managing model cached creating and desctuction by keys.
     * @var string
     */
    public $modelCachedKeys = 'object,action,version';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['object','action','version','jsonSchema','apiUser'];

    /**
     * Relation belog to ApiUser
     */
    public function apiUser()
    {
        return $this->belongsTo('App\ApiUser');
    }

    /**
     * Relation has many subscribers
     */
    public function subscribers() 
    {
        return $this->hasMany('App\Subscriber');
    }

    public static function getEvent($object,$action,$version)
    {
        $key = self::class.".".$object."_".$action."_".$version;
      
        //var_dump('get cached?');
        $event = Cache::rememberForever($key,function() use ($object,$action,$version) {
        $tmp = self::where('object', $object)
            ->where('action', $action)
            ->where('version', $version)
            ->first();
            // has this process, why not moved caching process to trait.
            if ($tmp && count($tmp->subscribers)>0) {
                $tmp->subscribers = $tmp->subscribers->keyBy('api_user_id');
            }
            //var_dump('from db');
            return $tmp;
        });
      
        return $event;
    }
}
