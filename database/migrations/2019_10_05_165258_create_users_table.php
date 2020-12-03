<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->integer('role_id')->nullable();
            $table->integer('supplier_id')->nullable();
            // Schema declaration
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('users');
    }
}
