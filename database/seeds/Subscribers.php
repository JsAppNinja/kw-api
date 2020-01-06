<?php

use Illuminate\Database\Seeder;

class Subscribers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subscribers')->truncate();
        factory('App\Subscriber', 20)->create();
    }
}
