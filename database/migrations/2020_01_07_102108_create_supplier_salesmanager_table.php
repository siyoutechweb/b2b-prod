<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSalesmanagerTable extends Migration
{

    public function up()
    {
        Schema::create('supplier_salesmanager', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->unsigned()->index();
            $table->integer('salesmanager_id')->unsigned()->index();
            $table->foreign('supplier_id')
                ->references('id')
                ->on('users');
            $table->foreign('salesmanager_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('supplier_salesmanager');
    }
}
