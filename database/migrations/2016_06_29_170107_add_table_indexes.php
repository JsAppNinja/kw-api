<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_users', function ($table) {
            $table->index('apiKey');
        });
        Schema::table('events', function ($table) {
            $table->index(['object','action','version']);
        });
        Schema::table('subscribers', function ($table) {
            $table->index(['event_id','api_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_users', function ($table) {
            $table->dropIndex(['apiKey']); 
        });
        Schema::table('events', function ($table) {
            $table->dropIndex(['object','action','version']); 
        });
        Schema::table('subscribers', function ($table) {
            $table->dropIndex(['event_id','api_user_id']); 
        });
    }
}
