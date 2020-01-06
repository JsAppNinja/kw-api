<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ApiUsers::class);
        $this->call(AppEvent::class);
        $this->call(Subscribers::class);
    }
}
