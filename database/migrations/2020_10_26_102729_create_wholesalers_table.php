<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWholesalersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesalers', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('shop_owner_id')->nullable();
            $table->string('wholesaler_name', 50);
            $table->string('company_name', 50);
            $table->string('description')->nullable();
            $table->string('email', 255)->nullable();
         
            $table->string('tax_number')->nullable();
            $table->string('contact')->nullable();
            
            $table->string('img_name')->nullable();
            $table->string('img_url')->nullable();
            $table->decimal('latitude', 17, 13)->nullable();
            $table->decimal('longitude', 17, 13)->nullable();
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
        Schema::dropIfExists('wholesalers');
    }
}
