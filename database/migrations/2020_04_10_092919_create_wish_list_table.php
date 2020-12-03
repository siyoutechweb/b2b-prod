<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWishListTable extends Migration
{

    public function up()
    {
        Schema::create('wish_list', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->bigInteger('product_base_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_base_id')->references('id')->on('product_base');
            

        });
    }

    public function down()
    {
        Schema::drop('wish_list');
    }
}
