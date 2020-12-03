<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountItemTable extends Migration
{

    public function up()
    {
        Schema::create('discount_item', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('discount_id');
            $table->integer('item_id');
            $table->decimal('discount_value',8,2)->nullable();
            $table->timestamp('start_date')->nullable() ;
            $table->timestamp('finish_date')->nullable();
            $table->integer('discount_barcode')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('discount_item');
    }
}
