<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('categories', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('category_name');
            $table->integer('parent_category_id')->nullable();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('categories');
    }
}
