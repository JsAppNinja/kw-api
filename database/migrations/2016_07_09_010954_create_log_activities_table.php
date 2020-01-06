<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_user_id')->unsigned()->nullable();
            $table->string('api_key')->nullable();
            $table->string('route_action_name');
            $table->string('method_called');
            $table->string('request_method');
            $table->text('request_object');
            $table->text('response_object');
            $table->integer('response_status_code');
            $table->text('response_body');
            $table->timestamp('created_at');


            // Create indicies
            $table->index(['api_user_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('log_activities');
    }
}
