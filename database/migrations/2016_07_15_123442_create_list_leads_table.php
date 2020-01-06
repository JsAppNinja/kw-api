<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('list_id');
            $table->string('list_agent_id');
            $table->string('router');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('list_leads');
    }
}
