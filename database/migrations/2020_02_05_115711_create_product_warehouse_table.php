<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('product_id')
                ->references('id')
                ->on('products');
            $table->foreign('user_id')
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
        Schema::dropIfExists('product_warehouse');
    }
}
