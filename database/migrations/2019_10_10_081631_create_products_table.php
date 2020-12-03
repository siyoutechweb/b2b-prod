<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{

    public function up()
    {
        Schema::create('products', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('product_name');
            $table->string('product_description');
            $table->string('product_image');
            $table->integer('quantity');
            $table->string('discount_type')->nullable();
            $table->float('product_price');
            $table->float('product_discount_price')->nullable();
            $table->float('product_weight')->nullable();
            $table->float('product_size')->nullable();
            $table->float('product_color')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('product_package')->nullable();
            $table->integer('product_box')->nullable();
            $table->string('product_barcode')->nullable();
        });
    }

    public function down()
    {
        Schema::drop('products');
    }
}
