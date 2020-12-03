<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProductCatalogTable extends Migration
{

    public function up()
    {
        Schema::create('product_catalog', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('catalog_id')->unsigned()->index();
            $table->foreign('product_id')
                ->references('id')
                ->on('products');
            $table->foreign('catalog_id')
                ->references('id')
                ->on('catalogs');
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('product_catalog');
    }
}
