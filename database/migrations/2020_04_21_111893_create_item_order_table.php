<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateItemOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_order', function (Blueprint $table) {
            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $table->increments('id');
            $table->integer('item_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('quantity')->unsigned()->nullable();
            $table->timestamps();
            // $table->foreign('item_id')
            //     ->references('id')
            //     ->on('product_items');
            // $table->foreign('order_id')
            //     ->references('id')
            //     ->on('orders');
            // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_order');
    }
}
