<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductImagesTable extends Migration
{

    public function up()
    {
        Schema::create('product_images', function(Blueprint $table) {
            $table->increments('id');
            $table->string('image_url');
            $table->integer('product_item_id')->nullable()->unsigned();
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
            $table->foreign('product_item_id')->references('id')->on('product_items');

        });
    }

    public function down()
    {
        Schema::drop('product_images');
    }
}
