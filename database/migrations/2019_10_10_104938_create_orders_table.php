<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('order_date')->nullable();
            $table->date('required_date')->nullable();
            $table->date('shipping_date')->nullable();
            $table->string('shipping_type')->nullable();
            $table->float('shipping_price')->nullable();
            $table->string('shipping_adresse')->nullable();
            $table->string('shipping_country')->nullable();
            $table->float('order_price');
            $table->unsignedInteger('commission')->nullable();
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('users');
            $table->unsignedInteger('shop_owner_id');
            $table->foreign('shop_owner_id')->references('id')->on('users');
            // $table->unsignedInteger('commission_id');
            // $table->foreign('commission_id')->references('id')->on('commissions');
            // $table->unsignedInteger('sales_manager_id')->nullable();
            // $table->foreign('sales_manager_id')->references('id')->on('users');
            $table->Integer('statut_id');
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('orders');
    }
}
