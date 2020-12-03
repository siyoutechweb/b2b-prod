<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_products', function (Blueprint $table) {
           
            $table->increments('id');
            $table->string('product_name');
            $table->bigInteger('product_barcode');
            $table->string('product_description')->nullable();
            $table->string('product_image')->nullable();
            $table->float('cost_price');
            $table->float('tax_rate')->default(0);
            $table->float('product_weight')->nullable();
            $table->float('product_size')->nullable();
            $table->float('product_color')->nullable();
            $table->string('supplier_id')->nullable();
        
            $table->integer('product_quantity')->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('purchase_products');
    }
}
