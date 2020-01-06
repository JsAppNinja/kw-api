<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('address',255);
            $table->string('city',100);
            $table->string('state',100);
            $table->string('zip',100);
            $table->string('dob',100);
            $table->string('age_range',100);
            $table->string('timestamp',100);
            $table->string('ethnic_group',100);
            $table->string('single_parent',100);
            $table->string('senior_adult_in_household',100);
            $table->string('young_adult_in_household',100);
            $table->string('working_woman',100);
            $table->string('soho_indicator',100);
            $table->string('business_owner',100);
            $table->string('language',100);
            $table->string('religion',100);
            $table->string('number_of_children',100);
            $table->string('marital_status_in_household',100);
            $table->string('home_owner_renter',100);
            $table->string('education',100);
            $table->string('occupation',100);
            $table->string('occupation_detail',100);
            $table->string('gender',100);
            $table->string('social_presence',100);
            $table->string('presence_of_children',100);
            
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
        Schema::drop('persons');
    }
}
