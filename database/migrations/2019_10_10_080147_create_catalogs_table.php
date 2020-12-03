<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsTable extends Migration
{

    public function up()
    {
        Schema::create('catalogs', function(Blueprint $table) {
            $table->increments('id');
            $table->string('catalog_name')->nullable();
            $table->string('catalog_description')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->date('catalog_start_time')->nullable();
            $table->date('catalog_end_time')->nullable();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('catalogs');
    }
}
