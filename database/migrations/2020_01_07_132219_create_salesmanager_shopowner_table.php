<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSalesmanagerShopownerTable extends Migration
{

    public function up()
    {
        Schema::create('salesmanager_shopowner', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('salesmanager_id')->unsigned()->index();
            $table->integer('shop_owner_id')->unsigned()->index();
            $table->foreign('salesmanager_id')
                ->references('id')
                ->on('users');
            $table->foreign('shop_owner_id')
                ->references('id')
                ->on('users');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('salesmanager_shopowner');
    }
}
