<?php

/**
 * Created by PhpStorm.
 * User: joshteam
 * Date: 6/25/16
 * Time: 3:54 PM
 */
class ApiUsers extends \Illuminate\Database\Seeder
{

    public function run()
    {
        DB::table('api_users')->truncate();
        factory('App\ApiUser', 10)->create();
    }

}