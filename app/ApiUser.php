<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ObserveCacheTrait;

class ApiUser extends Model
{
    use ObserveCacheTrait;

    /**
     * this used for ObserveCacheTrait, for managing model cached creating and desctuction by keys.
     * @var string
     */
    public $modelCachedKeys = 'apiKey';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['apiKey','company','application','isActive','email'];

	/**
     * Disable api user
     *
     * @return boolean
     */
	public function disable()
	{
		$this->isActive = 0;
		return $this->save();
	}

	/**
	 * enable api user
	 */
	public function enable()
	{
		$this->isActive = 1;
		return $this->save();
	}

	/**
	 * toggle is active status
	 */
	public function toggleActive()
	{
		$this->isActive = ($this->isActive)? 0:1;
		return $this->save();
	}

    public function events()
    {
        return $this->hasMany('App\Event');
    }
}
