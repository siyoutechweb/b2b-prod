<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToSupplierFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_funds', function (Blueprint $table) {
            //
            $table->integer('wholesaler_id')->unsigned()->nullable();
            $table->foreign('wholesaler_id')->references('id')->on('wholesalers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_funds', function (Blueprint $table) {
            //
        });
    }
}
