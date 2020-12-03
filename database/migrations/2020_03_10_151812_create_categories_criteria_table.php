<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesCriteriaTable extends Migration
{

    public function up()
    {
        Schema::create('categories_criteria', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->integer('criteria_id');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('categories_criteria');
    }
}
