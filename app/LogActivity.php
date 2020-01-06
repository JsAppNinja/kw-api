<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Log Activity Storage Model
 *
 * @property int $id
 * @property int $api_user_id
 * @property string $route_action_name
 * @property string $method_called
 * @property string $request_object
 * @property string $response_object
 * @property int $response_status_code
 * @property string $response_body
 * @property string $created_at
 *
 */
class LogActivity extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['api_key', 'api_user_id', 'route_action_name', 'method_called', 'request_method',
                'request_object', 'response_object', 'response_body', 'response_status_code'];
}
