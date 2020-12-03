<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiyoucommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siyoucommission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('commission_percent')->nullable();
            $table->float('commission_amount')->nullable();
            $table->float('Deposit')->nullable();
            $table->float('Deposit_rest')->nullable();
            $table->integer('order_id')->unsigned()->index()->nullable();
            $table->foreign('order_id')
            ->references('id')
            ->on('orders');
            $table->integer('supplier_id')->unsigned()->index();
            $table->foreign('supplier_id')
            ->references('id')
            ->on('users');
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
        Schema::dropIfExists('siyoucommission');
    }
}
