<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionItemTable extends Migration
{

    public function up()
    {
        Schema::create('commission_item', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('commission_id');
            $table->unsignedInteger('item_id');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('commission_item');
    }
}
