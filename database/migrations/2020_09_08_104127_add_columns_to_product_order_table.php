<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_order', function (Blueprint $table) {

            //
            $table->integer('product_id')->unsigned()->index();
            $table->integer('order_id')->unsigned()->index();
            $table->foreign('product_id')
            ->references('id')
            ->on('product_items');
        $table->foreign('order_id')
            ->references('id')
            ->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_order', function (Blueprint $table) {
            //
            $table->dropColumn(['product_id','order_id']);
        });
    }
}
