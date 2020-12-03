<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{

    public function up()
    {
        Schema::create('commissions', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('shop_owner_id')->nullable();
            $table->unsignedInteger('salesmanager_id');
            $table->float('commission_percent');
            $table->string('commission_type');
            $table->timestamps();
            // $table->foreign('shop_owner_id')->references('id')->on('users');
            // $table->unsignedInteger('sales_manager_id');
            // $table->foreign('sales_manager_id')->references('id')->on('users');
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('commissions');
    }
}
