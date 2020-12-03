<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{

    public function up()
    {
        Schema::create('discounts', function(Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('discounts');
    }
}
