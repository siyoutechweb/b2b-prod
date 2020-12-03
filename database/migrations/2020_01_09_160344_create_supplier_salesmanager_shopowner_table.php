<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSalesmanagerShopownerTable extends Migration
{

    public function up()
    {
        Schema::create('supplier_salesmanager_shop_owner', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('supplier_id')->unsigned()->index();
            $table->integer('salesmanager_id')->unsigned()->index()->nullable();
            $table->integer('shop_owner_id')->unsigned()->index()->nullable();
            $table->integer('commission_amount')->nullable();
            $table->foreign('supplier_id')
                ->references('id')
                ->on('users');
            $table->foreign('salesmanager_id')
                ->references('id')
                ->on('users');
            $table->foreign('shop_owner_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        Schema::drop('supplier_salesmanager_shop_owner');
    }
}
