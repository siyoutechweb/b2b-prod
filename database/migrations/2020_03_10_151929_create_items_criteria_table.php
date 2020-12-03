<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateItemsCriteriaTable extends Migration
{

    public function up()
    {
        Schema::create('items_criteria', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_item_id');
            $table->integer('criteria_id');
            $table->integer('criteria_unit_id')->nullable();
            $table->string('criteria_value');
            $table->timestamps();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('items_criteria');
    }
}
