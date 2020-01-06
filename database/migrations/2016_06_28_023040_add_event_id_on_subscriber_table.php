<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventIdOnSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function($table)
        {
            $table->integer('event_id')->after('id');
            $table->integer('api_user_id')->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribers', function($table)
        {
            $table->dropColumn('event_id');
            $table->dropColumn('api_user_id');
        });
    }
}
