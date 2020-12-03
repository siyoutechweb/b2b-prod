<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidesTable extends Migration
{

    public function up()
    {
        Schema::create('slides', function(Blueprint $table) {
            $table->increments('id');
            $table->text('slide_url')->nullable()->defaut("https://www.siyoutechnology.online/img/project-item-01.jpg");
            $table->string('slide_name')->nullable();
            $table->string('slide_title')->nullable()->defaut("SIYOU");
            $table->string('description')->nullable()->defaut("B2B Solution");
            $table->integer('supplier_id')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::drop('slides');
    }
}
