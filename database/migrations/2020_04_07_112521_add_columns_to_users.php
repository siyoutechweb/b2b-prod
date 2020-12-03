<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('phone_num1')->nullable()->after('password');
            $table->integer('phone_num2')->nullable()->after('phone_num1');
            $table->integer('tax_number')->nullable()->after('phone_num2');
            $table->string('first_resp_name')->nullable();
            $table->string('adress')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->integer('postal_code')->nullable();
            // $table->integer('longitude')->nullable();
            // $table->integer('latitude')->nullable();
            // $table->boolean('logistique_servie')->default(false)->after('product_visibility');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_num1','phone_num2','tax_number','first_resp_name',
            'adress','country','region','postal_code']);
        });
    }
}
