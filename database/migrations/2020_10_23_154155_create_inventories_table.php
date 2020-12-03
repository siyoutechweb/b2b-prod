<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
           
            $table->increments('id');
            $table->string('batch_number')->nullable();
            $table->string('operator')->nullable();
            $table->string('verifier')->nullable();
            $table->date('date')->nullable();
            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')->references('id')->on('supplier_warehouses')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->integer('operator_status')->unsigned()->nullable();
            $table->foreign('operator_status')->references('id')->on('inventory_status')->onDelete('cascade');
            $table->integer('verifier_status')->unsigned()->nullable();
            $table->foreign('verifier_status')->references('id')->on('inventory_status')->onDelete('cascade');
            $table->string('inventory_status')->nullable();
            
           
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
        Schema::dropIfExists('inventories');
    }
}
