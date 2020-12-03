<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCriteriaBaseTable extends Migration
{

    public function up()
    {
        Schema::create('criteria_base', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('criteria_base');
    }
}
