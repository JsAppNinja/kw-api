<?php
namespace App\Traits;

use App\Observers\ModelObserver;
use App\Traits\Query\Builder;
use Cache;

trait ObserveCacheTrait
{
    /**
     * trait boot
     */
    public static function bootObserveCacheTrait()
    {
        static::saved(function($model){
            $key = self::getCachedKey($model);
            Cache::forget($key);
        });
        static::deleted(function($model){
            $key = self::getCachedKey($model);
            Cache::forget($key);
        });
    }

    /**
     * get model cachedkey definition
     *
     * @return String
     */
    public static function getCachedKey($model)
    {
        // check model keys cached.
        $cachedKeys = $model->getKeyName();
        if ($model->modelCachedKeys) {
            $cachedKeys = $model->modelCachedKeys;
        }
        $keys = explode(",",$cachedKeys);
        $strs = [];
        foreach($keys as $key) {
            $key = trim($key);
            array_push($strs,$model->{$key}); 
        }
        $modelPrefix = get_class($model);
        if ($model->modelPrefix) {
            $modelPrefix = $model->modelPrefix;
        }
        $strkey = $modelPrefix.".".implode("_",$strs);
        return $strkey;
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();
        $builder = new Builder($conn, $grammar, $conn->getPostProcessor());

        $builder->modelCachedKeys($this->modelCachedKeys?:$this->getKeyName());
        $builder->prefix(get_class($this));

        if (isset($this->rememberFor)) {
            $builder->remember($this->rememberFor);
        }
        if (isset($this->rememberCacheTag)) {
            $builder->cacheTags($this->rememberCacheTag);
        }

        return $builder;
    }
}