<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionTrackingTable extends Migration
{

    public function up()
    {
        Schema::create('position_tracking', function(Blueprint $table) {
            $table->increments('id');
            $table->decimal('latitude', 17, 13);
            $table->decimal('longitude', 17, 13);
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('position_tracking');
    }
}
