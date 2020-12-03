<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCriteriaUnitsTable extends Migration
{

    public function up()
    {
        Schema::create('criteria_units', function(Blueprint $table) {
            $table->increments('id');
            $table->string('unit_name')->nullable();
            $table->integer('criteria_base_id');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('criteria_units');
    }
}
