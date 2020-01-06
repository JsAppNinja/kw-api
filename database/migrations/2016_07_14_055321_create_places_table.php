<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lat',100);
            $table->string('lng',100);
            $table->text('icon');
            $table->string('places_id',255);
            $table->string('name',255);
            $table->tinyInteger('open_now');
            $table->string('photo_height',100);
            $table->text('photo_html_attributes');
            $table->text('photo_reference');
            $table->string('photo_width',100);
            $table->string('place_id',100);
            $table->string('rating',100);
            $table->text('reference');
            $table->string('scope',100);
            $table->string('types',255);
            $table->string('vicinity',255);

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
        Schema::drop('places');
    }
}
