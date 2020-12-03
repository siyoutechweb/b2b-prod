<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_warehouses', function (Blueprint $table) {
           
            $table->increments('id');
            
            $table->string('name');
            $table->string('description');
            $table->string('first_responsible');
            $table->string('second_responsible');
            $table->decimal('longitude');
            $table->decimal('latitude');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('supplier_warehouses');
    }
}
