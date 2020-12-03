<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductBaseTable extends Migration
{

    public function up()
    {
        Schema::create('product_base', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->string('product_description')->nullable();
            $table->float('taxe_rate');
            $table->integer('category_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('product_base');
    }
}
