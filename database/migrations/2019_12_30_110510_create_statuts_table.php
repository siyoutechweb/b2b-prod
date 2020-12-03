<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateStatutsTable extends Migration
{

    public function up()
    {
        Schema::create('statuts', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('statut_name')->nullable();
            $table->string('description')->nullable();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('statuts');
    }
}
