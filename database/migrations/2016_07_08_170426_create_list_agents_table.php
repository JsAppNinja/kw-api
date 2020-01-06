<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('list_id');
            $table->string('agent_id')->unique();
            $table->boolean('leads_given');
            $table->dateTime('last_lead_received');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('list_agents');
    }
}
