<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductBrandsTable extends Migration
{

    public function up()
    {
        Schema::create('product_brands', function(Blueprint $table) {
            $table->increments('id');
            $table->string('brand_name');
            $table->string('brand_logo')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('product_brands');
    }
}
