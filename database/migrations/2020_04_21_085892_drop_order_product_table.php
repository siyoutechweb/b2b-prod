<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('item_order', function (Blueprint $table) {
        //     $table->dropForeign('item_order_item_id_foreign');
        //     $table->dropForeign('item_order_order_id_foreign');
        // });
        
        // Schema::dropIfExists('item_order');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
