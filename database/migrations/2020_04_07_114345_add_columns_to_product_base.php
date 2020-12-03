<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToProductBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_base', function (Blueprint $table) {
            $table->integer('product_package')->nullable()->after('taxe_rate');
            $table->integer('product_box')->nullable()->after('product_package');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_base', function (Blueprint $table) {
            $table->dropColumn(['product_package','product_box']);
        });
    }
}
