<?php

use Illuminate\Database\Seeder;

class AppEvent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->truncate();
        factory('App\Event', 20)->create();
    }
}
